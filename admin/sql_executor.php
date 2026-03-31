<?php
require_once 'include/auth.php';
require_once '../config/db.php';

$results = null;
$error = null;
$query = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['query'])) {
    $query = $_POST['query'];
    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        
        if (str_starts_with(strtolower(trim($query)), 'select') || str_starts_with(strtolower(trim($query)), 'show')) {
            $results = $stmt->fetchAll();
        } else {
            $results = "Success: " . $stmt->rowCount() . " row(s) affected.";
        }
    } catch (PDOException $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SQL Executor - Gaushala Admin</title>
    <?php include 'include/head.php'; ?>
</head>
<body class="min-h-screen flex flex-col md:flex-row bg-[#fdfaf7]">

    <?php include 'include/sidebar.php'; ?>

    <!-- Main Content Area -->
    <main class="flex-1 p-6 md:p-12 overflow-y-auto">
        <div class="max-w-6xl mx-auto">
            <header class="mb-10 flex justify-between items-center">
                <div>
                    <h1 style="font-family: 'Playfair Display';" class="text-4xl font-bold">SQL <span class="text-[#FF6A00] italic">Executor</span></h1>
                    <p class="text-gray-500 text-sm mt-2">Manage your database directly with SQL queries.</p>
                </div>
            </header>

        <section class="glass rounded-3xl p-8 shadow-2xl mb-10">
            <form method="POST">
                <label class="block text-sm font-bold mb-4 uppercase tracking-widest text-gray-400">Write SQL Query</label>
                <textarea name="query" 
                          class="w-full h-40 bg-white/50 border border-gray-200 rounded-2xl p-6 font-mono text-sm focus:outline-none focus:ring-2 focus:ring-[#FF6A00]/20 transition-all mb-6"
                          placeholder="CREATE TABLE announcements (...);"><?= htmlspecialchars($query) ?></textarea>
                <button type="submit" 
                        class="bg-[#FF6A00] text-white px-10 py-4 rounded-xl font-bold hover:shadow-xl hover:scale-105 transition-all">
                    Execute Query
                </button>
            </form>
        </section>

        <?php if ($error): ?>
            <div class="bg-red-50 border-l-4 border-red-500 p-6 rounded-2xl mb-10">
                <h3 class="text-red-800 font-bold mb-2">Query Error</h3>
                <p class="text-red-600 font-mono text-sm"><?= htmlspecialchars($error) ?></p>
            </div>
        <?php endif; ?>

        <?php if ($results): ?>
            <section class="glass rounded-3xl p-8 shadow-xl overflow-hidden">
                <h3 class="text-gray-400 font-bold mb-6 uppercase tracking-widest text-sm">Results</h3>
                <?php if (is_array($results) && count($results) > 0): ?>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm border-collapse">
                            <thead>
                                <tr class="border-b border-gray-100">
                                    <?php foreach (array_keys($results[0]) as $key): ?>
                                        <th class="py-4 px-4 font-bold text-nature"><?= htmlspecialchars($key) ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($results as $row): ?>
                                    <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition-colors">
                                        <?php foreach ($row as $val): ?>
                                            <td class="py-4 px-4 text-gray-600"><?= htmlspecialchars($val) ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php elseif (is_array($results)): ?>
                    <p class="text-gray-500 italic">No rows returned.</p>
                <?php else: ?>
                    <p class="text-green-600 font-bold"><?= htmlspecialchars($results) ?></p>
                <?php endif; ?>
            </section>
        <?php endif; ?>
    </div>
</main>
</body>
</html>
