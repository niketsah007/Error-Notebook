<?php
include 'db.php';

// Fetch all questions, ordering by newest first
$result = $conn->query("SELECT * FROM mistakes ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>All Mistakes History</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 font-sans p-8">
    <div class="max-w-4xl mx-auto">
        
        <!-- Header & Navigation -->
        <div class="flex justify-between items-center mb-8 bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-800">📚 Full History</h1>
                <p class="text-slate-500 mt-1">You have logged a total of <span class="font-bold text-indigo-500"><?php echo $result->num_rows; ?></span> questions.</p>
            </div>
            <div class="flex gap-3">
                <a href="index.php" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-2 px-6 rounded-lg transition-colors border border-slate-300">
                    📅 Daily Review
                </a>
                <a href="add_error.php" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-lg transition-colors shadow-md">
                    + Log Mistake
                </a>
            </div>
        </div>

        <!-- Question Cards -->
        <div class="space-y-6">
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="bg-white rounded-xl shadow-sm overflow-hidden border-l-4 border-slate-400">
                    <div class="p-6">
                        
                        <!-- Tags & Meta Info -->
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <span class="bg-slate-100 text-slate-600 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wide">
                                    <?php echo htmlspecialchars($row['subject']); ?>
                                </span>
                                <span class="bg-indigo-50 text-indigo-600 text-xs font-bold px-3 py-1 rounded-full flex items-center gap-1">
                                    📌 Type: <?php echo htmlspecialchars($row['error_type']); ?>
                                </span>
                            </div>
                            <div class="text-xs text-slate-400 font-semibold">
                                Next Review: <?php echo date("M d, Y", strtotime($row['next_review'])); ?>
                            </div>
                        </div>

                        <!-- Question & Image -->
                        <h2 class="text-lg font-semibold text-slate-800 mb-4"><?php echo nl2br(htmlspecialchars($row['question'])); ?></h2>
                        
                        <?php if(!empty($row['image_path'])): ?>
                            <div class="mb-4 rounded-lg overflow-hidden border border-slate-200 bg-slate-50 p-2 max-w-lg">
                                <img src="uploads/<?php echo $row['image_path']; ?>" alt="Question Screenshot" class="w-full h-auto rounded">
                            </div>
                        <?php endif; ?>

                        <!-- Solution Toggle -->
                        <details class="group mt-4">
                            <summary class="cursor-pointer text-indigo-600 font-medium hover:text-indigo-800 list-none flex items-center gap-2">
                                <svg class="w-5 h-5 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                View AI Solution
                            </summary>
                            <div class="mt-3 p-4 bg-slate-50 border border-slate-200 rounded-lg text-slate-700 whitespace-pre-wrap leading-relaxed"><?php echo htmlspecialchars($row['solution']); ?></div>
                        </details>

                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>