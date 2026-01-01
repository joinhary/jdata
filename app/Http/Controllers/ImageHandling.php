<?php


namespace App\Http\Controllers;


use Illuminate\Http\Request;


trait ImageHandling
{
    /**
     * Hàm xử lý ảnh
     * @param Request $request
     * @param string $path
     * @return array
     */
    public function addImage(Request $request, $path='images/suutra', $input='pic'){
        $save_path = $path;
        $i = 0;
        $choosen_img = [];
        $image = $request->file($input);

        foreach ( $image as $item){
            $img_name = time() + $i . '.' . $item->getClientOriginalExtension();
            $choosen_img[] = $img_name;
            $item->storeAs('suutra/', $img_name);
            $i++;
        }
        return $choosen_img;
    }
	public function getRealName(Request $request, $path='images/suutra', $input='pic'){
        $save_path = $path;
        $i = 0;
        $choosen_img = [];
        $image = $request->file($input);

        foreach ( $image as $item){
            $img_name = $item->getClientOriginalName();
            $choosen_img[] = $img_name;

        }
        return $choosen_img;
    }

//    public function removeImage(Request $request){
//        $save_path = 'images/lylich';
//        $img = $request->lylich_hinhanh;
//        //Kiểm tra điều kiện tồn tại và xóa file ảnh cũ
//        if ($img != '' && file_exists(public_path($save_path . '/' . $img))) {
//            unlink(public_path($save_path . '/' . $img));
//            $status = 1;
//            return $status;
//        }
//        else{
//            $status = 0;
//            return $status;
//        }
//    }
}
