<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\Province;
use App\Models\District;
use App\Models\Ward;

class VietnamZoneSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Đang tải dữ liệu Địa chính Việt Nam từ API... Vui lòng đợi!');

        // Gọi API mở lấy toàn bộ dữ liệu (cấp độ 3 gồm cả Phường/Xã)
        $response = Http::get('https://provinces.open-api.vn/api/?depth=3');
        
        if ($response->successful()) {
            $provinces = $response->json();
            
            DB::beginTransaction();
            try {
                foreach ($provinces as $p) {
                    // Tạo Tỉnh
                    $province = Province::create(['name' => $p['name'], 'code' => $p['code']]);
                    
                    foreach ($p['districts'] as $d) {
                        // Tạo Quận/Huyện
                        $district = District::create(['name' => $d['name'], 'province_id' => $province->id]);
                        
                        $wardData = [];
                        foreach ($d['wards'] as $w) {
                            // Gom mảng Phường/Xã lại để Insert 1 lần cho nhanh (Tránh bị đơ máy vì có 10.000 phường)
                            $wardData[] = [
                                'name' => $w['name'],
                                'district_id' => $district->id,
                                'created_at' => now(),
                                'updated_at' => now()
                            ];
                        }
                        Ward::insert($wardData); // Insert theo lô
                    }
                }
                DB::commit();
                $this->command->info('Tuyệt vời! Đã nạp xong 63 Tỉnh thành, Huyện và Xã/Phường vào Database!');
            } catch (\Exception $e) {
                DB::rollBack();
                $this->command->error('Có lỗi xảy ra: ' . $e->getMessage());
            }
        } else {
            $this->command->error('Không thể kết nối đến API. Vui lòng kiểm tra lại mạng.');
        }
    }
}