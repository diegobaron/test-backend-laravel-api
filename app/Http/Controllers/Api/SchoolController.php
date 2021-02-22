<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\School;

class SchoolController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $data = School::paginate(10);
        } catch(\Exception $e) {
            return $this->respondException($e);
        }
        return response()->json($data, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $data = $request->only([
                'name',
                'city'
            ]);
            $school = new School();
            $validator = $school->validator($data, 'create');
            if($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], 200);
            }
            $data = $school->create($data);
        } catch(\Exception $e) {
            return $this->respondException($e);
        }
        return response()->json($data, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $data = School::with('courses')->find($id);
        } catch(\Exception $e) {
            return $this->respondException($e);
        }
        return response()->json($data, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $data = $request->only([
                'name',
                'city'
            ]);
            $school = new School();
            $validator = $school->validator($data, 'update');
            if($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], 200);
            }
            $school->where('id', $id)->update($data);
        } catch(\Exception $e) {
            return $this->respondException($e);
        }
        return response()->json(['message' => 'Success'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            School::where('id', $id)->delete();
        } catch(\Exception $e) {
            return $this->respondException($e);
        }
        return response()->json(['message' => 'Success'], 200);
    }

    /**
     * @param \Exception $e
     * @return \Illuminate\Http\Response
     */
    protected function respondException($exception)
    {
        return response()->json([
            'error' => true,
            'exception' => $exception->getMessage()
        ], 500);
    }
}
