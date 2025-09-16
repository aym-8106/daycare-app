<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>勤怠打刻 - CareNavi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Hiragino Kaku Gothic Pro', 'ヒラギノ角ゴ Pro W3', Meiryo, メイリオ, Osaka, 'MS PGothic', sans-serif;
        }

        .punch-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            margin: 20px;
            padding: 30px;
        }

        .time-display {
            font-size: 2.5rem;
            font-weight: bold;
            color: #2c3e50;
            text-align: center;
            margin-bottom: 10px;
        }

        .date-display {
            font-size: 1.2rem;
            color: #7f8c8d;
            text-align: center;
            margin-bottom: 30px;
        }

        .punch-btn {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            border: none;
            font-size: 1.5rem;
            font-weight: bold;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
            margin: 20px auto;
            display: block;
        }

        .punch-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.2);
        }

        .check-in-btn {
            background: linear-gradient(135deg, #4CAF50, #45a049);
            color: white;
        }

        .check-out-btn {
            background: linear-gradient(135deg, #f44336, #d32f2f);
            color: white;
        }

        .status-card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            padding: 20px;
            margin: 15px 0;
        }

        .location-info {
            background: rgba(52, 152, 219, 0.1);
            border-left: 4px solid #3498db;
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
        }

        .photo-preview {
            max-width: 100px;
            max-height: 100px;
            border-radius: 10px;
            margin: 10px 0;
        }

        .qr-scanner-area {
            background: #f8f9fa;
            border: 2px dashed #dee2e6;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="punch-card">
                    <!-- 現在時刻表示 -->
                    <div class="time-display" id="currentTime"></div>
                    <div class="date-display" id="currentDate"></div>

                    <!-- ユーザー情報 -->
                    <div class="text-center mb-4">
                        <h4><?= $user['staff_name'] ?>さん</h4>
                        <small class="text-muted"><?= $user['company_name'] ?></small>
                    </div>

                    <!-- 今日の勤怠状況 -->
                    <?php if (isset($attendance_data) && $attendance_data): ?>
                        <div class="status-card">
                            <h6><i class="fas fa-clock me-2"></i>今日の勤怠</h6>
                            <div class="row">
                                <?php if ($attendance_data['work_time'] && $attendance_data['work_time'] != '0000-00-00 00:00:00'): ?>
                                    <div class="col-6">
                                        <small class="text-muted">出勤</small>
                                        <div class="fw-bold"><?= date('H:i', strtotime($attendance_data['work_time'])) ?></div>
                                    </div>
                                <?php endif; ?>
                                <?php if ($attendance_data['leave_time'] && $attendance_data['leave_time'] != '0000-00-00 00:00:00'): ?>
                                    <div class="col-6">
                                        <small class="text-muted">退勤</small>
                                        <div class="fw-bold"><?= date('H:i', strtotime($attendance_data['leave_time'])) ?></div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- 打刻ボタン -->
                    <div class="text-center">
                        <?php if (!isset($attendance_data['work_time']) || $attendance_data['work_time'] == '0000-00-00 00:00:00'): ?>
                            <button class="punch-btn check-in-btn" onclick="showCheckInModal()">
                                <i class="fas fa-play mb-2"></i><br>
                                出勤
                            </button>
                        <?php elseif (!isset($attendance_data['leave_time']) || $attendance_data['leave_time'] == '0000-00-00 00:00:00'): ?>
                            <button class="punch-btn check-out-btn" onclick="showCheckOutModal()">
                                <i class="fas fa-stop mb-2"></i><br>
                                退勤
                            </button>
                        <?php else: ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i> 本日の勤務は完了しています
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- QRコード読み取り -->
                    <div class="text-center mt-4">
                        <button class="btn btn-outline-primary" onclick="startQRScanner()">
                            <i class="fas fa-qrcode me-2"></i>QRコードで打刻
                        </button>
                    </div>

                    <!-- 位置情報 -->
                    <div class="location-info" id="locationInfo" style="display: none;">
                        <small class="text-muted"><i class="fas fa-map-marker-alt me-1"></i>現在地</small>
                        <div id="locationText"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 出勤モーダル -->
    <div class="modal fade" id="checkInModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">出勤打刻</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="checkInForm" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">出勤時刻</label>
                            <input type="datetime-local" class="form-control" name="work_time" id="workTime" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">メモ（任意）</label>
                            <textarea class="form-control" name="memo" rows="3" placeholder="特記事項があれば入力してください"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">写真（任意）</label>
                            <input type="file" class="form-control" name="check_in_photo" accept="image/*" capture="camera">
                            <small class="text-muted">職場の様子などを撮影してください</small>
                        </div>
                        <input type="hidden" name="location" id="checkInLocation">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
                    <button type="button" class="btn btn-success" onclick="submitCheckIn()">
                        <i class="fas fa-play me-1"></i>出勤打刻
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- 退勤モーダル -->
    <div class="modal fade" id="checkOutModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">退勤打刻</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="checkOutForm" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">退勤時刻</label>
                            <input type="datetime-local" class="form-control" name="leave_time" id="leaveTime" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">メモ（任意）</label>
                            <textarea class="form-control" name="memo" rows="3" placeholder="業務報告や特記事項があれば入力してください"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">写真（任意）</label>
                            <input type="file" class="form-control" name="check_out_photo" accept="image/*" capture="camera">
                            <small class="text-muted">退勤時の職場の様子などを撮影してください</small>
                        </div>
                        <input type="hidden" name="location" id="checkOutLocation">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
                    <button type="button" class="btn btn-danger" onclick="submitCheckOut()">
                        <i class="fas fa-stop me-1"></i>退勤打刻
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- QRスキャナーモーダル -->
    <div class="modal fade" id="qrScannerModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">QRコード読み取り</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="qr-scanner-area">
                        <div id="qr-reader" style="width: 100%;"></div>
                        <p class="mt-3">QRコードをカメラに向けてください</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script>
        let currentLocation = null;

        // 現在時刻を更新
        function updateTime() {
            const now = new Date();
            document.getElementById('currentTime').textContent = now.toLocaleTimeString('ja-JP', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            document.getElementById('currentDate').textContent = now.toLocaleDateString('ja-JP', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                weekday: 'long'
            });
        }

        // 位置情報を取得
        function getCurrentLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        currentLocation = {
                            latitude: position.coords.latitude,
                            longitude: position.coords.longitude,
                            accuracy: position.coords.accuracy
                        };

                        // 住所に変換（簡易版）
                        document.getElementById('locationInfo').style.display = 'block';
                        document.getElementById('locationText').textContent =
                            `緯度: ${currentLocation.latitude.toFixed(6)}, 経度: ${currentLocation.longitude.toFixed(6)}`;
                    },
                    function(error) {
                        console.log('位置情報の取得に失敗しました:', error.message);
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 600000
                    }
                );
            }
        }

        // 出勤モーダル表示
        function showCheckInModal() {
            const now = new Date();
            document.getElementById('workTime').value = now.toISOString().slice(0, 16);
            document.getElementById('checkInLocation').value = currentLocation ? JSON.stringify(currentLocation) : '';
            new bootstrap.Modal(document.getElementById('checkInModal')).show();
        }

        // 退勤モーダル表示
        function showCheckOutModal() {
            const now = new Date();
            document.getElementById('leaveTime').value = now.toISOString().slice(0, 16);
            document.getElementById('checkOutLocation').value = currentLocation ? JSON.stringify(currentLocation) : '';
            new bootstrap.Modal(document.getElementById('checkOutModal')).show();
        }

        // 出勤打刻送信
        function submitCheckIn() {
            const formData = new FormData(document.getElementById('checkInForm'));

            fetch('<?= base_url('attendance/insert_work_time') ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('出勤打刻が完了しました。');
                    location.reload();
                } else {
                    alert('エラー: ' + data.message);
                }
            })
            .catch(error => {
                alert('通信エラーが発生しました。');
                console.error('Error:', error);
            });
        }

        // 退勤打刻送信
        function submitCheckOut() {
            const formData = new FormData(document.getElementById('checkOutForm'));

            fetch('<?= base_url('attendance/update_leave_time') ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('退勤打刻が完了しました。お疲れさまでした。');
                    location.reload();
                } else {
                    alert('エラー: ' + data.message);
                }
            })
            .catch(error => {
                alert('通信エラーが発生しました。');
                console.error('Error:', error);
            });
        }

        // QRスキャナー開始
        function startQRScanner() {
            const modal = new bootstrap.Modal(document.getElementById('qrScannerModal'));
            modal.show();

            const html5QrcodeScanner = new Html5QrcodeScanner(
                "qr-reader",
                { fps: 10, qrbox: {width: 250, height: 250} },
                false
            );

            html5QrcodeScanner.render(
                function(decodedText, decodedResult) {
                    // QRコードが読み取れた場合
                    processQRCode(decodedText);
                    html5QrcodeScanner.clear();
                    modal.hide();
                },
                function(error) {
                    // エラー処理（通常は無視）
                    console.warn(`QR Code scan error: ${error}`);
                }
            );
        }

        // QRコード処理
        function processQRCode(qrData) {
            fetch('<?= base_url('attendance/qr_punch') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    qr_code: qrData,
                    punch_type: <?= (!isset($attendance_data['work_time']) || $attendance_data['work_time'] == '0000-00-00 00:00:00') ? '"check_in"' : '"check_out"' ?>,
                    location: currentLocation
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('QRコードでの打刻が完了しました。');
                    location.reload();
                } else {
                    alert('エラー: ' + data.message);
                }
            });
        }

        // 初期化
        document.addEventListener('DOMContentLoaded', function() {
            updateTime();
            setInterval(updateTime, 1000);
            getCurrentLocation();
        });
    </script>
</body>
</html>