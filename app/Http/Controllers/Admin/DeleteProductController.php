<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DeleteProductController extends Controller
{
    public function delete(Request $request)
    {
        try {
            // التحقق من البيانات المدخلة
            $validator = Validator::make($request->all(), [
                'ProductId' => 'required|integer|min:1',
                'adminEmail' => 'required|email',
                'ProductImages' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'Status' => 'Invalid input',
                    'errors' => $validator->errors(),
                ], 400);
            }

            $productId = $request->input('ProductId');
            $adminEmail = $request->input('adminEmail');
            $productImages = $request->input('ProductImages');

            // تحميل قائمة المشرفين
            $supervisorEmails = include base_path('services/AdminList.php');

            if (!in_array($adminEmail, $supervisorEmails)) {
                return response()->json(['Status' => 'Not authorized'], 403);
            }

            // حذف الصور إذا كانت موجودة
            if (!empty($productImages)) {
                $productImages = str_replace("'", '"', $productImages); // إصلاح التنسيق
                $imagesArray = json_decode($productImages, true);

                if (is_array($imagesArray)) {
                    foreach ($imagesArray as $image) {
                        $filename = basename($image);
                        $path = public_path("storage/productImages/$filename"); // ✅ تعديل المسار الصحيح

                        if (file_exists($path)) {
                            unlink($path);
                        }
                    }
                }
            }


            // تنفيذ عملية الحذف داخل Transaction
            DB::beginTransaction();

            DB::table('products')->where('productId', $productId)->delete();
            DB::table('biddings')->where('product_id', $productId)->delete();

            DB::commit();

            return response()->json(['Status' => 'Product deleted'], 200);
        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            return response()->json([
                'Status' => 'Error',
                'Message' => $e->getMessage(),
            ], 500);
        }
    }
}
