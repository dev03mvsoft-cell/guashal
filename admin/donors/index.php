<?php
require_once '../include/auth.php';
require_once '../include/functions.php';
require_once '../../config/db.php';

// Handle Delete Action
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    // Fetch image to delete from server
    $stmt = $pdo->prepare("SELECT profile_pic FROM donors WHERE id = ?");
    $stmt->execute([$id]);
    $donor = $stmt->fetch();

    if ($donor && $donor['profile_pic'] !== 'default_donor.png') {
        cleanup_file('asset/img/donors/' . $donor['profile_pic']);
    }

    $pdo->prepare("DELETE FROM donors WHERE id = ?")->execute([$id]);
    header("Location: index.php?msg=Member removed from Donate Wall");
    exit();
}

// Handle Bulk Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'bulk_delete') {
    if (!empty($_POST['selected_ids'])) {
        $ids = $_POST['selected_ids'];
        try {
            // First cleanup images
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $stmt = $pdo->prepare("SELECT profile_pic FROM donors WHERE id IN ($placeholders)");
            $stmt->execute($ids);
            $donors_to_delete = $stmt->fetchAll();

            foreach ($donors_to_delete as $d) {
                if ($d['profile_pic'] !== 'default_donor.png') {
                    cleanup_file('asset/img/donors/' . $d['profile_pic']);
                }
            }

            // Then delete records
            $stmt = $pdo->prepare("DELETE FROM donors WHERE id IN ($placeholders)");
            $stmt->execute($ids);
            header("Location: index.php?msg=" . count($ids) . " donors purged from registry");
            exit();
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}

$all_items = $pdo->query("SELECT * FROM donors ORDER BY donation_date DESC")->fetchAll();
$message = $_GET['msg'] ?? '';
$error = '';

// Helper for special date notification
function get_days_remaining($date)
{
    if (!$date) return 999;
    $today = new DateTime('today');
    $special = new DateTime($date);
    $special->setDate((int)$today->format('Y'), (int)$special->format('m'), (int)$special->format('d'));

    if ($special < $today) $special->modify('+1 year');
    return (int)$today->diff($special)->days;
}

// Advanced Priority Sorting Logic
usort($all_items, function ($a, $b) {
    $a_special = get_days_remaining($a['special_date'] ?? null);
    $a_anniv = get_days_remaining($a['donation_date'] ?? null);
    $a_min = min($a_special, $a_anniv);

    $b_special = get_days_remaining($b['special_date'] ?? null);
    $b_anniv = get_days_remaining($b['donation_date'] ?? null);
    $b_min = min($b_special, $b_anniv);

    // Decision Logic: Primary sort by notification windows (0, 2, 7)
    $a_score = ($a_min == 0) ? 0 : (($a_min <= 2) ? 1 : (($a_min <= 7) ? 2 : 3));
    $b_score = ($b_min == 0) ? 0 : (($b_min <= 2) ? 1 : (($b_min <= 7) ? 2 : 3));

    if ($a_score !== $b_score) return $a_score <=> $b_score;

    // Secondary sort: Days remaining within the same window
    if ($a_min !== $b_min) return $a_min <=> $b_min;

    // Tertiary sort: Recent original donation date
    return strtotime($b['donation_date']) <=> strtotime($a['donation_date']);
});

$donors = $all_items;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donors Donate Wall Management | Admin</title>
    <?php include '../include/head.php'; ?>
    <style>
        .donor-table tr {
            transition: all 0.2s ease-in-out;
        }
    </style>
</head>

<body class="flex bg-[#fcfdfd]">

    <?php include '../include/sidebar.php'; ?>

    <main class="flex-1 p-6 md:p-12 overflow-x-hidden">

        <!-- Header Section -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-12">
            <div>
                <span class="text-saffron font-black uppercase tracking-[0.3em] text-[13px] mb-2 block">Gratitude Registry</span>
                <h1 style="font-family: 'Playfair Display';" class="text-4xl font-bold text-nature mb-2">Donors Donate Wall</h1>
                <p class="text-nature/60 font-medium">Manage and celebrate the compassionate souls who support our cows.</p>
            </div>
            <div class="flex items-center gap-4">
                <form id="bulk-form" method="POST" onsubmit="return confirmAction(event, 'Purge selected donors?', 'The records of these charitable souls will be removed from the Donate Wall.');">
                    <input type="hidden" name="action" value="bulk_delete">
                    <div id="bulk-delete-btn" style="display: none;" class="items-center gap-4 bg-red-50 text-red-600 px-6 py-3 rounded-2xl animate-fade-in border border-red-100 shadow-xl shadow-red-500/10">
                        <span class="text-[12px] font-black uppercase tracking-widest">Selected: <span id="selected-count">0</span></span>
                        <button type="submit" class="bg-red-600 text-white w-10 h-10 rounded-xl flex items-center justify-center hover:scale-110 transition-transform">
                            <i class="fas fa-trash-alt text-[12px]"></i>
                        </button>
                    </div>
                </form>
                <a href="editor.php" class="bg-saffron text-white px-8 py-4 rounded-xl font-bold flex items-center gap-3 shadow-lg shadow-saffron/20 hover:scale-105 transition-all">
                    <i class="fas fa-plus"></i> Add New Donor
                </a>
            </div>
        </div>

        <?php if ($message): ?>
            <div class="bg-nature/5 text-nature p-4 rounded-2xl border border-nature/10 mb-8 flex items-center gap-4 animate-fade-in text-sm font-bold uppercase tracking-widest">
                <i class="fas fa-check-circle text-green-500"></i>
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="bg-red-50 text-red-600 p-4 rounded-2xl border border-red-100 mb-8 flex items-center gap-4 text-sm font-bold">
                <i class="fas fa-exclamation-triangle"></i>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <!-- Donors Table View -->
        <div class=" rounded-[1.5rem]  border border-nature/10 overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left donor-table border-collapse">
                    <thead>
                        <tr class="bg-nature text-white text-[13px] uppercase font-black tracking-[0.15em] shadow-sm">
                            <th class="px-8 py-6 border-b border-white/10 w-10">
                                <input type="checkbox" onchange="toggleSelectAll(this, 'multi-select-item')" class="w-5 h-5 rounded border-gray-300 text-saffron focus:ring-saffron bg-white/10">
                            </th>
                            <th class="px-8 py-6 border-b border-white/10">Donor Profile</th>
                            <th class="px-8 py-6 border-b border-white/10">Contact & Outreach</th>
                            <th class="px-8 py-6 border-b border-white/10">Offering Details</th>
                            <th class="px-8 py-6 border-b border-white/10">Special Occasion</th>
                            <th class="px-8 py-6 border-b border-white/10">Visibility</th>
                            <th class="px-8 py-6 border-b border-white/10 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-nature/10">
                        <?php foreach ($donors as $donor):
                            $special_date = $donor['special_date'] ?? null;
                            $days_to_special = get_days_remaining($special_date);
                            $is_alert = ($days_to_special <= 7);
                        ?>
                            <tr class="bg-white hover:bg-nature/[0.05] transition-all duration-200 <?= $is_alert ? '!bg-amber-50/50' : '' ?> group">
                                <td class="px-8 py-7">
                                    <input type="checkbox" name="selected_ids[]" value="<?= $donor['id'] ?>" form="bulk-form" onchange="updateBulkButtonVisibility()" class="multi-select-item w-5 h-5 rounded border-gray-300 text-saffron focus:ring-saffron">
                                </td>
                                <td class="px-8 py-7">
                                    <div class="flex items-center gap-5">
                                        <div class="w-16 h-16 rounded-2xl overflow-hidden border-2 border-nature/10 shadow-sm flex-shrink-0">
                                            <img src="../../asset/img/donors/<?= $donor['profile_pic'] ?>"
                                                onerror="this.src='/asset/img/donors/default_donor.png'"
                                                class="w-full h-full object-cover">
                                        </div>
                                        <div>
                                            <div class="font-medium text-nature uppercase text-[15px] leading-none mb-2 tracking-tight"><?= htmlspecialchars($donor['name']) ?></div>
                                            <div class="text-[11px] text-nature/60 uppercase font-normal tracking-widest leading-none flex items-center gap-2">
                                                <i class="fas fa-calendar-check text-saffron"></i> Donated: <?= date('M d, Y', strtotime($donor['donation_date'])) ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex flex-col gap-3">
                                        <?php if (!empty($donor['contact'])): ?>
                                            <!-- Phone & Primary Icon -->
                                            <div class="flex items-center gap-2 mb-1">
                                                <div class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center text-white shadow-lg shadow-green-500/20">
                                                    <i class="fab fa-whatsapp text-[10px]"></i>
                                                </div>
                                                <div class="text-nature font-medium text-[12px] tracking-widest"><?= htmlspecialchars($donor['contact']) ?></div>
                                            </div>

                                            <?php
                                            $wa_phone = preg_replace('/[^0-9]/', '', $donor['contact']);
                                            $anniv_days = get_days_remaining($donor['donation_date']);
                                            $bday_days = get_days_remaining($donor['special_date'] ?? null);
                                            $min_days = min($anniv_days, $bday_days);

                                            // 🚨 EMERGENCY / TODAY ACTIONS
                                            if ($min_days === 0):
                                                $event_name = ($anniv_days === 0) ? "Anniversary" : "Birthday";
                                                $purpose_text = !empty($donor['purpose']) ? " for " . $donor['purpose'] : "";

                                                if ($anniv_days === 0) {
                                                    $msg = "Shree Radhe Radhe ! Respected " . $donor['name'] . " ji, today is the sacred date of your noble donation" . $purpose_text . ". Every act of Gau Seva, whether done in celebration or sentimental memory, is a supreme blessing. We remain grateful for your kindness.";
                                                } else {
                                                    $msg = "Shree Radhe Radhe ! Noble " . $donor['name'] . " ji, we wish you a very Happy Birthday! May your kindness toward our sacred Gaia bring you infinite joy and auspiciousness.";
                                                }
                                            ?>
                                                <a href="https://wa.me/<?= $wa_phone ?>?text=<?= str_replace(' ', '%20', $msg) ?>" target="_blank" class="flex items-center justify-center gap-2 bg-nature text-white py-2.5 rounded-xl font-medium uppercase text-[10px] tracking-tight shadow-xl hover:bg-black transition-all animate-pulse">
                                                    ✨ Send <?= $event_name ?> Wish
                                                </a>

                                            <?php
                                            // ⚠️ 2-DAY REMINDER ACTIONS
                                            elseif ($min_days <= 2):
                                                $event_name = ($anniv_days <= 2) ? "Anniversary" : "Birthday";
                                                $purpose_text = ($anniv_days <= 2 && !empty($donor['purpose'])) ? " for " . $donor['purpose'] : "";

                                                if ($anniv_days <= 2) {
                                                    $msg = "Shree Radhe Radhe ! Respected " . $donor['name'] . " ji, apki donation ki date close aa chuki hai. We are remembering your selfless support" . $purpose_text . " and praying for your well-being.";
                                                } else {
                                                    $msg = "Shree Radhe Radhe ! Noble " . $donor['name'] . " ji, we are excited as your Birthday is coming in 2 days! We are praying for your health and happiness.";
                                                }
                                            ?>
                                                <a href="https://wa.me/<?= $wa_phone ?>?text=<?= str_replace(' ', '%20', $msg) ?>" target="_blank" class="flex items-center justify-center gap-2 bg-amber-500 text-white py-2 rounded-xl font-medium uppercase text-[9px] tracking-tighter shadow-md hover:bg-amber-600 transition-all">
                                                    ⏳ Send 2-Day Reminder
                                                </a>

                                            <?php
                                            // 📩 REGULAR CONNECT
                                            else:
                                                $msg = "Shree Radhe Radhe ! " . $donor['name'] . " ji, we were just thinking of your kind support for our sacred cows. Hope you are well!";
                                            ?>
                                                <a href="https://wa.me/<?= $wa_phone ?>?text=<?= str_replace(' ', '%20', $msg) ?>" target="_blank" class="text-nature/80 hover:text-nature font-medium uppercase text-[10px] tracking-widest flex items-center gap-2 p-1 transition-all">
                                                    <i class="fas fa-paper-plane text-green-600"></i> Quick Message
                                                </a>
                                            <?php endif; ?>

                                        <?php else: ?>
                                            <a href="editor.php?id=<?= $donor['id'] ?>" class="text-[10px] bg-nature/5 text-nature/40 font-medium uppercase py-2 px-4 rounded-lg border border-dashed border-nature/10 hover:bg-nature/10 transition-all">
                                                <i class="fas fa-plus mr-1"></i> Add Contact
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="text-saffron font-medium text-lg">₹<?= number_format($donor['amount']) ?></div>
                                    <div class="text-[10px] text-nature/50 font-normal italic line-clamp-1 mb-2">"<?= htmlspecialchars($donor['purpose']) ?>"</div>

                                    <!-- Donation Anniversary Logic -->
                                    <?php
                                    $anniv_days = get_days_remaining($donor['donation_date']);
                                    if ($anniv_days <= 7):
                                    ?>
                                        <?php if ($anniv_days == 0): ?>
                                            <span class="bg-indigo-600 text-white text-[8px] font-medium uppercase px-2 py-0.5 rounded-full animate-bounce">Donation Anniv. Today! 🌿</span>
                                        <?php else: ?>
                                            <span class="bg-nature/10 text-nature text-[8px] font-medium uppercase px-2 py-0.5 rounded-full border border-nature/10">Anniv. in <?= $anniv_days ?> Days</span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                                <td class="px-8 py-6">
                                    <?php if (!empty($donor['special_date'])): ?>
                                        <div class="flex flex-col gap-1">
                                            <div class="text-nature font-medium text-xs"><?= date('M d', strtotime($donor['special_date'])) ?></div>
                                            <?php if ($days_to_special <= 7): ?>
                                                <?php if ($days_to_special == 0): ?>
                                                    <span class="bg-saffron text-white text-[9px] font-medium uppercase px-2 py-0.5 rounded-full w-fit animate-pulse">It's Today! 🎉</span>
                                                <?php elseif ($days_to_special <= 2): ?>
                                                    <span class="bg-red-500 text-white text-[9px] font-medium uppercase px-2 py-0.5 rounded-full w-fit shadow-md">2 Days Left ⚠️</span>
                                                <?php elseif ($days_to_special <= 7): ?>
                                                    <span class="bg-amber-500 text-white text-[9px] font-medium uppercase px-2 py-0.5 rounded-full w-fit">7 Days Left 🕒</span>
                                                <?php else: ?>
                                                    <span class="text-[9px] text-nature/30 uppercase font-medium"><?= $days_to_special ?> Days to go</span>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <a href="editor.php?id=<?= $donor['id'] ?>" class="text-[9px] text-nature/20 uppercase font-medium border border-dashed border-nature/10 px-2 py-1 rounded hover:bg-nature/5 transition-all">
                                            <i class="fas fa-calendar-plus mr-1"></i> Set Date
                                        </a>
                                    <?php endif; ?>
                                </td>
                                <td class="px-8 py-6">
                                    <?php if ($donor['is_visible']): ?>
                                        <span class="flex items-center gap-2 text-green-600 text-[10px] font-medium uppercase">
                                            <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span> Public
                                        </span>
                                    <?php else: ?>
                                        <span class="flex items-center gap-2 text-gray-400 text-[10px] font-medium uppercase">
                                            <span class="w-2 h-2 rounded-full bg-gray-300"></span> Private
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <div class="flex justify-end gap-3">
                                        <a href="editor.php?id=<?= $donor['id'] ?>" class="w-10 h-10 bg-nature/5 text-nature rounded-xl flex items-center justify-center hover:bg-nature hover:text-white transition-all shadow-sm">
                                            <i class="fas fa-edit text-sm"></i>
                                        </a>
                                        <form action="index.php?delete=<?= $donor['id'] ?>" method="POST" onsubmit="return confirmAction(event, 'Release Record?', 'This donor will be removed from the Donate Wall.');" class="inline">
                                            <button type="submit" class="w-10 h-10 bg-red-50 text-red-600 rounded-xl flex items-center justify-center hover:bg-red-600 hover:text-white transition-all shadow-sm">
                                                <i class="fas fa-trash text-sm"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if (empty($donors)): ?>
                <div class="py-32 text-center">
                    <i class="fas fa-heart-crack text-6xl text-nature/10 mb-6"></i>
                    <h3 class="text-2xl font-bold text-nature">The Donate Wall is Empty</h3>
                    <p class="text-nature/40 mt-2">Start recognizing your noble donors today.</p>
                    <a href="editor.php" class="inline-block mt-8 bg-nature text-white px-8 py-3 rounded-xl font-bold">Add First Donor</a>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>

</html>