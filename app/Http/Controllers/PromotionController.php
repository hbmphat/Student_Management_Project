<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    // 1. Hiển thị danh sách
    public function index(\Illuminate\Http\Request $request)
    {
        // Nếu gọi qua AJAX thì trả về JSON
        if ($request->ajax()) {
            $promotions = \App\Models\Promotion::orderBy('id', 'desc')->get();
            return response()->json($promotions);
        }

        // Nếu lỡ truy cập thẳng URL thì báo lỗi hoặc chuyển về trang học phí
        return redirect()->route('tuitions.index');
    }

    // 2. Lấy thông tin 1 KM (dùng cho Modal Sửa)
    public function show($id)
    {
        $promotion = Promotion::findOrFail($id);
        return response()->json($promotion);
    }

    // 3. Thêm mới
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'discount_percent' => 'required|numeric|min:1|max:100',
        ]);

        Promotion::create([
            'name' => $request->name,
            'discount_percent' => $request->discount_percent,
            'description' => $request->description,
            'is_active' => true, // Mặc định tạo ra là được bật
        ]);

        return response()->json(['success' => true, 'message' => 'Thêm chương trình khuyến mãi thành công!']);
    }

    // 4. Cập nhật
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'discount_percent' => 'required|numeric|min:1|max:100',
        ]);

        $promotion = Promotion::findOrFail($id);
        $promotion->update([
            'name' => $request->name,
            'discount_percent' => $request->discount_percent,
            'description' => $request->description,
        ]);

        return response()->json(['success' => true, 'message' => 'Cập nhật khuyến mãi thành công!']);
    }

    // 5. Bật/Tắt Khuyến mãi
    public function toggleActive($id)
    {
        $promotion = Promotion::findOrFail($id);
        $promotion->is_active = !$promotion->is_active;
        $promotion->save();

        $status = $promotion->is_active ? 'đã được bật' : 'đã bị tắt';
        return response()->json(['success' => true, 'message' => "Chương trình {$promotion->name} {$status}!"]);
    }

    // 6. Xóa (Thu hồi)
    public function destroy($id)
    {
        $promotion = Promotion::findOrFail($id);
        $promotion->delete();

        return response()->json(['success' => true, 'message' => 'Đã xóa chương trình khuyến mãi!']);
    }
}
