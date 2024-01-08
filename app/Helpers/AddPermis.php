<?php

use App\Models\FolderPermission;
use App\Models\UserProject;
if (function_exists('AddFolderPermissions')) {
    function AddFolderPermissions($folder_id,$user_id,$project_id)
    {
        //Thêm mới folder permission nếu thêm mới folder
        //lấy tất cả user trong project
        $userproject = UserProject::where('ProjectID', $project_id)->get();

        $folderPermissionData = $userproject->map(function ($item) use ($folder_id) {
            return [
                'UserID' => $item->UserID,
                'FolderID' => $folder_id,
            ];
        });
        
        FolderPermission::insert($folderPermissionData->toArray());
    }
}
