<?php
include 'db.php';
$today = date('Y-m-d');
$result = $conn->query("SELECT * FROM mistakes WHERE next_review <= '$today'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Study Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 font-sans p-8">
    <div class="max-w-4xl mx-auto">
        
        <!-- Header -->
        <div class="flex justify-between items-center mb-8 bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-800">Review Dashboard</h1>
                <p class="text-slate-500 mt-1">You have <span class="font-bold text-red-500"><?php echo $result->num_rows; ?></span> questions to review today.</p>
            </div>
            <a href="add_error.php" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-lg transition-colors shadow-md">
                + Log Mistake
            </a>
        </div>

        <!-- Empty State -->
        <?php if($result->num_rows == 0): ?>
            <div class="text-center py-16 bg-white rounded-xl shadow-sm border border-slate-200">
                <span class="text-5xl">🎉</span>
                <h3 class="text-xl font-bold text-slate-700 mt-4">All caught up!</h3>
                <p class="text-slate-500">You've completed all your scheduled reviews for today.</p>
            </div>
        <?php endif; ?>

        <!-- Question Cards -->
        <div class="space-y-6">
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="bg-white rounded-xl shadow-md overflow-hidden border-l-4 border-indigo-500">
                    <div class="p-6">
                        
                        <!-- Tags -->
                        <div class="flex items-center gap-3 mb-3">
                            <span class="bg-slate-100 text-slate-600 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wide">
                                <?php echo htmlspecialchars($row['subject']); ?>
                            </span>
                            <span class="bg-red-50 text-red-600 text-xs font-bold px-3 py-1 rounded-full flex items-center gap-1">
                                🤖 AI: <?php echo htmlspecialchars($row['error_type']); ?>
                            </span>
                        </div>

                        <!-- Question & Image -->
                        <h2 class="text-lg font-semibold text-slate-800 mb-4"><?php echo nl2br(htmlspecialchars($row['question'])); ?></h2>
                        
                        <?php if(!empty($row['image_path'])): ?>
                            <div class="mb-4 rounded-lg overflow-hidden border border-slate-200 bg-slate-50 p-2 max-w-lg">
                                <img src="uploads/<?php echo $row['image_path']; ?>" alt="Question Screenshot" class="w-full h-auto rounded">
                            </div>
                        <?php endif; ?>

                        <!-- Solution Toggle (Native HTML) -->
                        <details class="group mt-4">
                            <summary class="cursor-pointer text-indigo-600 font-medium hover:text-indigo-800 list-none flex items-center gap-2">
                                <svg class="w-5 h-5 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                Show Solution & Notes
                            </summary>
                            <div class="mt-3 p-4 bg-emerald-50 border border-emerald-100 rounded-lg text-emerald-900 whitespace-pre-wrap"><?php echo htmlspecialchars($row['solution']); ?></div>
                        </details>

                    </div>
                    
                    <!-- Action Bar -->
                    <div class="bg-slate-50 px-6 py-4 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-sm text-slate-500 font-medium">Did you remember it this time?</span>
                        <form action="process_review.php" method="POST" class="flex gap-3">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <input type="hidden" name="current_interval" value="<?php echo $row['interval_days']; ?>">
                            
                            <button type="submit" name="status" value="wrong" class="bg-white hover:bg-red-50 text-red-600 border border-red-200 font-semibold py-2 px-4 rounded-lg transition-colors">
                                ❌ Forgot (Reset)
                            </button>
                            <button type="submit" name="status" value="correct" class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2 px-6 rounded-lg transition-colors shadow-sm">
                                ✅ Got it!
                            </button>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>