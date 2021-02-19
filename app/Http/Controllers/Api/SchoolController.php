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
            return response()->json(School::paginate(10), 200);
        } catch(\Exception $e) {
            return $this->respondException($e);
        }
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
            return response()->json($school->create($data), 200);
        } catch(\Exception $e) {
            return $this->respondException($e);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            return response()->json(School::with('courses')->find($id), 200);
        } catch(\Exception $e) {
            return $this->respondException($e);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
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
            return response()->json(['message' => 'Success'], 200);
        } catch(\Exception $e) {
            return $this->respondException($e);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            School::where('id', $id)->delete();
            return response()->json(['message' => 'Success'], 200);
        } catch(\Exception $e) {
            return $this->respondException($e);
        }
    }

    /**
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondException(\Exception $e)
    {
        return response()->json([
            'error' => true,
            'exception' => $e->getMessage()
        ], 500);
    }
}
