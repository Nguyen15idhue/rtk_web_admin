# Tính năng Offset Tọa độ Trạm (Station Coordinate Offset)

## Mô tả
Tính năng này cho phép tự động thêm độ lệch ngẫu nhiên vào tọa độ của các trạm RTK khi lưu vào database. Điều này giúp bảo mật vị trí thực tế của các trạm bằng cách làm lệch tọa độ khoảng 2km so với vị trí ban đầu.

## Cách hoạt động
- Khi dữ liệu trạm được đồng bộ từ API RTK về database
- Tọa độ gốc (latitude, longitude) sẽ được áp dụng một độ lệch ngẫu nhiên
- Độ lệch được tính toán theo hướng ngẫu nhiên (0-360°) với khoảng cách cố định (~2km)
- Tọa độ đã lệch sẽ được lưu vào database thay vì tọa độ gốc

## Cấu hình

### File: `/private/config/settings.php`

```php
// Bật/tắt tính năng offset tọa độ
define('ENABLE_COORDINATE_OFFSET', true);  // true = bật, false = tắt

// Khoảng cách offset (đơn vị: km)
define('COORDINATE_OFFSET_DISTANCE_KM', 2.0);  // Mặc định 2km
```

## Các file liên quan

1. **StationModel.php** - Chứa phương thức `applyCoordinateOffset()`
2. **account_api.php** - Áp dụng offset khi đồng bộ dữ liệu trạm
3. **settings.php** - Cấu hình bật/tắt và khoảng cách offset

## Phương thức chính

### `StationModel::applyCoordinateOffset($originalLat, $originalLng)`

**Tham số:**
- `$originalLat` (float): Vĩ độ gốc
- `$originalLng` (float): Kinh độ gốc

**Trả về:**
- Array chứa tọa độ đã offset: `['lat' => float, 'lng' => float]`

**Ví dụ:**
```php
$stationModel = new StationModel();
$offset = $stationModel->applyCoordinateOffset(21.0285, 105.8542);
// Kết quả: ['lat' => 21.038048, 'lng' => 105.870570]
```

## Công thức tính toán

1. **Chuyển đổi khoảng cách:** 1° vĩ độ ≈ 111km
2. **Chuyển đổi kinh độ:** 1° kinh độ ≈ 111km × cos(vĩ độ)
3. **Tạo hướng ngẫu nhiên:** 0-360°
4. **Tính offset:**
   - Vĩ độ: `(khoảng_cách_km / 111) × sin(góc)`
   - Kinh độ: `(khoảng_cách_km / (111 × cos(vĩ_độ))) × cos(góc)`

## Test

Để kiểm tra tính năng, chạy script test:

```bash
cd /private/scripts
php test_coordinate_offset.php
```

## Lưu ý bảo mật

- Tọa độ gốc từ API **KHÔNG** được lưu vào database
- Chỉ tọa độ đã offset mới được lưu trữ
- Độ lệch là ngẫu nhiên, không thể đoán trước
- Có thể điều chỉnh khoảng cách offset theo nhu cầu

## Tắt tính năng

Để tắt tính năng và sử dụng tọa độ gốc:

```php
define('ENABLE_COORDINATE_OFFSET', false);
```

## Log

Mọi lần áp dụng offset sẽ được ghi log vào file error.log để theo dõi:

```
StationModel::applyCoordinateOffset - Original: (21.0285, 105.8542) -> Offset: (21.038048, 105.870570) - Distance: ~2km, Angle: 45.5°
```
