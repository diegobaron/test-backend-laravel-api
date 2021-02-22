<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $data = Course::with('school')->paginate(10);
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
                'school_id',
                'description',
                'start_date'
            ]);
            $course = new Course();
            $validator = $course->validator($data, 'create');
            if($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], 200);
            }
            $data = $course->create($data);
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
            $data = Course::with('school')->find($id);
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
                'school_id',
                'description',
                'start_date'
            ]);
            $course = new Course();
            $validator = $course->validator($data, 'update');
            if($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], 200);
            }
            $course->where('id', $id)->update($data);
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
            Course::where('id', $id)->delete();
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
