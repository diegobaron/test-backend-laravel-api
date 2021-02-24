<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;
use League\Csv\Statement;

class CourseQuotationController extends Controller
{   
    private $url_csv = "https://www4.bcb.gov.br/Download/fechamento/";
    private const BRAZILIAN_CURRENCY = 'BRL';
    private const FOREIGN_CURRENCIES = ['USD', 'AUD', 'EUR'];
    private $courses;
    private $quotes;

    public function quotation()
    {
        try {
            //$conversor = new ConversorMoeda();
            $courses = Http::get("https://exports.allyhub.co");
            $courses = json_decode($courses->body(), true);
            $quotes = $this->getCurrencyQuotes();
            $this->setQuotes($quotes);
            $this->setCourses($courses);

            $this->calculateCoins();

            return $this->getCourses();
        } catch(\Exception $e) {
            return response([
                'error' => true,
                'message' => 'error when generating quotes',
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'exception' => $e->getMessage()
            ], 500);
        }
    }

    private function setCourses($courses) {
        $this->courses = $courses;
    }
    
    private function getCourses() {
        return $this->courses;
    }
    
    private function setQuotes($quotes) {
        $this->quotes = $quotes;
    }

    /**
     *  Pegar cotacoes da receita federal
     */
    private function getCurrencyQuotes()
    {   
        $quotes = [];
        $date = $this->getQuoteDate();
        $filename = $date.".csv";
        $path = $this->saveTemporaryFile($filename);
        $stream = fopen(storage_path('app/public/'.$path), 'r');
        Storage::delete($path);

        $csv = Reader::createFromStream($stream);
        $csv->setDelimiter(';');
        $csv->setHeaderOffset(null);
        $stmt = (new Statement());
        $records = $stmt->process($csv, ['data', 'codigo', 'tipo', 'moeda', 'taxa_compra', 'taxa_venda', 'paridade_compra', 'paridade_venda']);
        foreach($records as $record) {
            if(!in_array($record['moeda'], self::FOREIGN_CURRENCIES)) continue;
            $quotes[$record['moeda']] = floatval(str_replace(',', '.', $record['taxa_venda']));
        }
        fclose($stream);
        return $quotes;
    }

    private function getQuoteDate()
    {
        $date = date("Y-m-d");
        $weekday = date("w", strtotime($date));
        if($weekday == '6') {
            $date = date("Ymd", strtotime("-1 days", strtotime($date)));
        } elseif($weekday == '0') {
            $date = date("Ymd", strtotime("-2 days", strtotime($date)));
        } else {
            $date = date("Ymd");
        }
        try {
            file_get_contents($this->url_csv.$date.".csv");
        } catch(\Exception $e) {
            $date = date("Ymd", strtotime("-1 days", strtotime($date)));
        }
        return $date;
    }

    private function saveTemporaryFile($filename) :string
    {   
        $path = 'temp/'.$filename;
        Storage::put($path, file_get_contents($this->url_csv.$filename));
        return $path;
    }    
    
    private function calculateCoins() {
        for($i = 0; $i < count($this->courses); $i++){
            $this->courses[$i] = $this->getCalculatedCourse($this->courses[$i]);
        }
    }
    
    private function getOriginalCurrency($course){
        foreach(self::FOREIGN_CURRENCIES as $coin){
            if($course[$coin] != "")
                return $coin;
        }
        throw new \Exception('Nenhum valor original do curso');
    }
    
    private function getCalculatedCourse($course){
        if($course[self::BRAZILIAN_CURRENCY] == ''){
            $moedaComValor = $this->getOriginalCurrency($course);
            $valorOriginal = floatval(str_replace(',', '.', $course[$moedaComValor]));
            $valorEmReal = ($valorOriginal * $this->quotes[$moedaComValor]);
            $course[self::BRAZILIAN_CURRENCY] = $valorEmReal;
        }
        
        foreach(self::FOREIGN_CURRENCIES as $coin){
            if($course[$coin] != "") continue;
            $course[$coin] = floatval(str_replace(',', '.', $course[self::BRAZILIAN_CURRENCY])) / $this->quotes[$coin];
        }
        
        return $course;
    }
}
