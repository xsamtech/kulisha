<?php

namespace App\Http\Controllers\API;

use App\Models\File;
use Illuminate\Http\Request;
use App\Http\Resources\File as ResourcesFile;

/**
 * @author Xanders
 * @see https://team.xsamtech.com/xanderssamoth
 */
class FileController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $files = File::orderByDesc('created_at')->paginate(30);
        $count_files = File::count();

        return $this->handleResponse(ResourcesFile::collection($files), __('notifications.find_all_files_success'), $files->lastPage(), $count_files);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Get inputs
        $inputs = [
            'file_name' => $request->file_name,
            'file_url' => $request->file_url,
            'type_id' => $request->type_id,
            'post_id' => $request->post_id,
            'message_id' => $request->message_id,
            'event_id' => $request->event_id,
            'user_id' => $request->user_id
        ];

        // Validate required fields
        if (trim($inputs['file_url']) == null) {
            return $this->handleError(__('miscellaneous.found_value') . ' ' . $inputs['file_url'], __('validation.custom.url.required'), 400);
        }

        if (!is_numeric($inputs['type_id']) == null OR trim($inputs['type_id'])) {
            return $this->handleError(__('miscellaneous.found_value') . ' ' . $inputs['type_id'], __('validation.custom.type.required'), 400);
        }

        $file = File::create($inputs);

        return $this->handleResponse(new ResourcesFile($file), __('notifications.create_file_success'));
    }

    /**
     * Display the specified resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $file = File::find($id);

        if (is_null($file)) {
            return $this->handleError(__('notifications.find_file_404'));
        }

        return $this->handleResponse(new ResourcesFile($file), __('notifications.find_file_success'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\File  $file
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, File $file)
    {
        // Get inputs
        $inputs = [
            'file_name' => $request->file_name,
            'file_url' => $request->file_url,
            'type_id' => $request->type_id,
            'post_id' => $request->post_id,
            'message_id' => $request->message_id,
            'event_id' => $request->event_id,
            'user_id' => $request->user_id
        ];

        if (trim($inputs['file_name']) != null) {
            $file->update([
                'file_name' => $inputs['file_name'],
                'updated_at' => now()
            ]);
        }

        if ($inputs['file_url'] != null) {
            $file->update([
                'file_url' => $inputs['file_url'],
                'updated_at' => now()
            ]);
        }

        if ($inputs['type_id'] != null) {
            $file->update([
                'type_id' => $inputs['type_id'],
                'updated_at' => now()
            ]);
        }

        if ($inputs['post_id'] != null) {
            $file->update([
                'post_id' => $inputs['post_id'],
                'updated_at' => now()
            ]);
        }

        if ($inputs['message_id'] != null) {
            $file->update([
                'message_id' => $inputs['message_id'],
                'updated_at' => now()
            ]);
        }

        if ($inputs['event_id'] != null) {
            $file->update([
                'event_id' => $inputs['event_id'],
                'updated_at' => now()
            ]);
        }

        if ($inputs['user_id'] != null) {
            $file->update([
                'user_id' => $inputs['user_id'],
                'updated_at' => now()
            ]);
        }

        return $this->handleResponse(new ResourcesFile($file), __('notifications.update_file_success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\File  $file
     * @return \Illuminate\Http\Response
     */
    public function destroy(File $file)
    {
        $file->delete();

        $files = File::orderByDesc('created_at')->paginate(30);
        $count_files = File::count();

        return $this->handleResponse(ResourcesFile::collection($files), __('notifications.delete_file_success'), $files->lastPage(), $count_files);
    }
}
