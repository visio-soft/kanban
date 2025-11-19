<?php

namespace Visiosoft\Kanban\Database\Seeders;

use Illuminate\Database\Seeder;
use Visiosoft\Kanban\Models\Board;
use Visiosoft\Kanban\Models\Issue;

class KanbanSeeder extends Seeder
{
    public function run(): void
    {
        // Create Boards
        $boards = [
            [
                'name' => 'Saha İşleri',
                'description' => 'Saha ekibi tarafından gerçekleştirilecek işler',
                'color' => '#3b82f6', // Blue
                'order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Yazılım İşleri',
                'description' => 'Yazılım geliştirme ve bakım işleri',
                'color' => '#8b5cf6', // Purple
                'order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Müşteri Talepleri',
                'description' => 'Müşterilerden gelen talepler ve özellik istekleri',
                'color' => '#ec4899', // Pink
                'order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Altyapı & DevOps',
                'description' => 'Sunucu, veritabanı ve altyapı işleri',
                'color' => '#14b8a6', // Teal
                'order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Acil Müdahale',
                'description' => 'Acil durum ve kritik hatalar',
                'color' => '#ef4444', // Red
                'order' => 5,
                'is_active' => true,
            ],
        ];

        foreach ($boards as $boardData) {
            $board = Board::firstOrCreate(
                ['name' => $boardData['name']],
                $boardData
            );

            // Create sample issues for each board
            $this->createSampleIssues($board);
        }
    }

    private function createSampleIssues(Board $board): void
    {
        $issuesByBoard = [
            'Saha İşleri' => [
                [
                    'title' => 'Park alanı kamera montajı',
                    'description' => 'Yeni park alanına 4 adet kamera monte edilecek. Kablo çekimi ve konfigürasyon dahil.',
                    'status' => 'backlog',
                    'priority' => 'high',
                    'order' => 1,
                    'tags' => ['donanım', 'montaj', 'kamera'],
                ],
                [
                    'title' => 'Bariyer bakım ve onarım',
                    'description' => 'Ana girişteki bariyerin motor bakımı yapılacak ve sensör ayarları kontrol edilecek.',
                    'status' => 'in_progress',
                    'priority' => 'medium',
                    'order' => 2,
                    'due_date' => now()->addDays(3),
                    'tags' => ['bakım', 'bariyer'],
                ],
                [
                    'title' => 'LED ekran kurulumu',
                    'description' => 'Otopark girişine bilgilendirme LED ekranı kurulacak.',
                    'status' => 'backlog',
                    'priority' => 'low',
                    'order' => 3,
                    'tags' => ['donanım', 'led'],
                ],
            ],
            'Yazılım İşleri' => [
                [
                    'title' => 'Plaka tanıma algoritması iyileştirmesi',
                    'description' => 'ALPR algoritmasının doğruluk oranını artırmak için yeni model entegrasyonu yapılacak.',
                    'status' => 'in_progress',
                    'priority' => 'high',
                    'order' => 1,
                    'due_date' => now()->addDays(7),
                    'tags' => ['ai', 'alpr', 'geliştirme'],
                ],
                [
                    'title' => 'Raporlama modülü geliştirme',
                    'description' => 'Yöneticiler için detaylı raporlama ve analiz modülü geliştirilecek.',
                    'status' => 'backlog',
                    'priority' => 'medium',
                    'order' => 2,
                    'tags' => ['feature', 'raporlama'],
                ],
                [
                    'title' => 'Mobil uygulama API güncellemesi',
                    'description' => 'Mobil uygulama için yeni endpoint\'ler eklenecek ve mevcut API\'ler optimize edilecek.',
                    'status' => 'in_progress',
                    'priority' => 'high',
                    'order' => 3,
                    'due_date' => now()->addDays(5),
                    'tags' => ['api', 'mobil', 'backend'],
                ],
                [
                    'title' => 'Ödeme sistemi entegrasyonu',
                    'description' => 'Yeni ödeme sağlayıcısı entegre edilecek ve test edilecek.',
                    'status' => 'done',
                    'priority' => 'high',
                    'order' => 4,
                    'tags' => ['ödeme', 'entegrasyon'],
                ],
            ],
            'Müşteri Talepleri' => [
                [
                    'title' => 'Abonelik paket özelleştirmesi',
                    'description' => 'Müşteri özel abonelik paketi talep etti. Fiyatlandırma ve özellikler belirlenecek.',
                    'status' => 'backlog',
                    'priority' => 'medium',
                    'order' => 1,
                    'tags' => ['abonelik', 'özelleştirme'],
                ],
                [
                    'title' => 'SMS bildirim özelliği',
                    'description' => 'Araç giriş/çıkışlarında SMS bildirimi gönderilmesi isteniyor.',
                    'status' => 'in_progress',
                    'priority' => 'medium',
                    'order' => 2,
                    'due_date' => now()->addDays(10),
                    'tags' => ['feature', 'sms', 'bildirim'],
                ],
                [
                    'title' => 'Beyaz liste toplu yükleme',
                    'description' => 'Excel dosyasından toplu plaka yükleme özelliği eklenecek.',
                    'status' => 'done',
                    'priority' => 'low',
                    'order' => 3,
                    'tags' => ['feature', 'import'],
                ],
            ],
            'Altyapı & DevOps' => [
                [
                    'title' => 'Veritabanı yedekleme sistemi',
                    'description' => 'Otomatik veritabanı yedekleme ve disaster recovery planı oluşturulacak.',
                    'status' => 'in_progress',
                    'priority' => 'high',
                    'order' => 1,
                    'due_date' => now()->addDays(4),
                    'tags' => ['veritabanı', 'yedekleme', 'güvenlik'],
                ],
                [
                    'title' => 'Sunucu kapasite artırımı',
                    'description' => 'Artan kullanıcı sayısı için sunucu kapasitesi artırılacak.',
                    'status' => 'backlog',
                    'priority' => 'high',
                    'order' => 2,
                    'tags' => ['sunucu', 'performans'],
                ],
                [
                    'title' => 'CI/CD pipeline iyileştirmesi',
                    'description' => 'Deployment sürecini hızlandırmak için pipeline optimize edilecek.',
                    'status' => 'done',
                    'priority' => 'medium',
                    'order' => 3,
                    'tags' => ['devops', 'ci/cd'],
                ],
            ],
            'Acil Müdahale' => [
                [
                    'title' => 'Kamera bağlantı hatası',
                    'description' => '3 numaralı kamera bağlantı hatası veriyor. Acil kontrol gerekiyor.',
                    'status' => 'in_progress',
                    'priority' => 'high',
                    'order' => 1,
                    'due_date' => now()->addHours(4),
                    'tags' => ['acil', 'kamera', 'hata'],
                ],
                [
                    'title' => 'Ödeme sistemi çalışmıyor',
                    'description' => 'Kredi kartı ödemeleri başarısız oluyor. Acil müdahale gerekiyor.',
                    'status' => 'done',
                    'priority' => 'high',
                    'order' => 2,
                    'tags' => ['acil', 'ödeme', 'kritik'],
                ],
            ],
        ];

        if (! isset($issuesByBoard[$board->name])) {
            return;
        }

        foreach ($issuesByBoard[$board->name] as $issueData) {
            $issueData['board_id'] = $board->id;

            Issue::firstOrCreate(
                [
                    'board_id' => $board->id,
                    'title' => $issueData['title'],
                ],
                $issueData
            );
        }
    }
}
