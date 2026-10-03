<?php
include 'db.php';

// ⚠️ PASTE YOUR GOOGLE AI STUDIO API KEY HERE
$GEMINI_API_KEY = "YOUR_GEMINNI_API_KEY"; 

function askGeminiToSolve($questionText, $imagePath, $apiKey) {
    if (empty($apiKey) || $apiKey == "Your Api Key") {
        return ['category' => 'No API Key', 'solution' => 'Please add your Gemini API key in the code.'];
    }
    
    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-3.5-flash-lite:generateContent?key=" . $apiKey;
    
    $prompt = "You are an expert exam tutor. I am providing a question via text, an image, or both.\n";
    $prompt .= "1. Provide a detailed, step-by-step solution to the problem.\n";
    $prompt .= "2. Categorize the type of problem/concept (e.g., 'Algebra', 'Syllogism', 'Physics - Motion', 'Calculation Error', 'Grammar - Error Spotting', 'Reading Comprehension').\n";
    $prompt .= "Respond EXACTLY in this JSON format and nothing else: {\"category\": \"The Category\", \"solution\": \"The Step-by-Step Solution\"}";
    
    if (!empty($questionText)) {
        $prompt .= "\nUser added this context/text: " . $questionText;
    }
    
    $parts = [['text' => $prompt]];
    
    if (!empty($imagePath) && file_exists($imagePath)) {
        $mime = mime_content_type($imagePath);
        $base64 = base64_encode(file_get_contents($imagePath));
        $parts[] = [
            'inlineData' => [
                'mimeType' => $mime,
                'data' => $base64
            ]
        ];
    }
    
	$data = [
    'contents' => [['parts' => $parts]],
    'generationConfig' => ['responseMimeType' => 'application/json']
	];    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Prevents the "1" response
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POST, true); // Prevents the 404 error
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    
    // Local Laragon SSL Bypass
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    
    $response = curl_exec($ch);
    $curl_err = curl_error($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    // Diagnostic Checks
    if ($curl_err) {
        return ['category' => 'cURL Error', 'solution' => 'Server failed to connect: ' . $curl_err];
    }
    
    if ($http_code != 200) {
        return ['category' => 'API Error ' . $http_code, 'solution' => 'Google responded with: ' . $response];
    }
    
    $result = json_decode($response, true);
    $ai_text = $result['candidates'][0]['content']['parts'][0]['text'] ?? '{"category": "Parse Error", "solution": "Blank response from Google."}';
    
    $ai_text = str_replace(['```json', '```'], '', $ai_text);
    return json_decode(trim($ai_text), true);
}					

if(isset($_POST['submit'])) {
    $subject = $_POST['subject'];
    $question = $conn->real_escape_string($_POST['question']);
    $user_solution = $_POST['solution'];
    $error_type = $_POST['error_type'];
    $next_review = date('Y-m-d', strtotime('+1 day'));
    
    $image_path = NULL;
    $target_dir = NULL;
    if(isset($_FILES['screenshot']) && $_FILES['screenshot']['error'] == 0) {
        $ext = pathinfo($_FILES['screenshot']['name'], PATHINFO_EXTENSION);
        $new_filename = time() . '_' . rand(1000,9999) . '.' . $ext;
        $target_dir = "uploads/" . $new_filename;
        
        if(move_uploaded_file($_FILES['screenshot']['tmp_name'], $target_dir)) {
            $image_path = $new_filename;
        }
    }

    if (empty($user_solution) || $error_type == 'Auto-Detect with AI') {
        $ai_response = askGeminiToSolve($question, $target_dir, $GEMINI_API_KEY);
        
        if (empty($user_solution)) {
            $user_solution = $conn->real_escape_string($ai_response['solution'] ?? 'Solution generation failed.');
        } else {
            $user_solution = $conn->real_escape_string($user_solution);
        }
        
        if ($error_type == 'Auto-Detect with AI') {
            $error_type = $conn->real_escape_string($ai_response['category'] ?? 'Unclassified');
        } else {
            $error_type = $conn->real_escape_string($error_type);
        }
    } else {
        $user_solution = $conn->real_escape_string($user_solution);
        $error_type = $conn->real_escape_string($error_type);
    }

    $sql = "INSERT INTO mistakes (subject, question, error_type, solution, interval_days, next_review, image_path) 
            VALUES ('$subject', '$question', '$error_type', '$user_solution', 1, '$next_review', '$image_path')";
    
    if($conn->query($sql)) {
        echo "<script>alert('Mistake Logged successfully! AI provided the solution.'); window.location='index.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>AI Tutor - Log Mistake</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-6">
    <div class="bg-white rounded-xl shadow-lg p-8 w-full max-w-2xl border-t-4 border-indigo-600">
        
        <!-- Navigation Menu -->
        <div class="flex justify-between items-center mb-6 pb-4 border-b border-slate-100">
            <h2 class="text-2xl font-bold text-slate-800">🤖 Smart Logger</h2>
            <div class="flex gap-2">
                <a href="index.php" class="text-sm bg-slate-100 hover:bg-slate-200 text-slate-700 py-2 px-3 rounded-lg font-medium transition">📅 Daily Review</a>
                <a href="all_mistakes.php" class="text-sm bg-indigo-50 hover:bg-indigo-100 text-indigo-700 py-2 px-3 rounded-lg font-medium transition">📚 All History</a>
            </div>
        </div>

        <p class="text-slate-500 mb-6 text-sm">Upload a screenshot. Leave the solution blank to let AI solve it.</p>
        
        <form method="POST" enctype="multipart/form-data" class="space-y-5">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700">Subject</label>
                    <select name="subject" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm p-2 border focus:ring-indigo-500 focus:border-indigo-500">
                        <option>Maths</option><option>Reasoning</option><option>General Science</option><option>Computer Knowledge</option><option>English</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Problem Type</label>
                    <select name="error_type" class="mt-1 block w-full rounded-md shadow-sm p-2 border bg-indigo-50 font-semibold text-indigo-700 border-indigo-200">
                        <option value="Auto-Detect with AI">✨ Let AI Categorize</option>
                        <option>Calculation Error</option>
                        <option>Forgot Formula</option>
                        <option>Silly Mistake</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">Question Screenshot (Required for AI Vision)</label>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-300 border-dashed rounded-md bg-slate-50 hover:bg-slate-100 transition-colors">
                    <div class="space-y-1 text-center">
                        <svg class="mx-auto h-12 w-12 text-slate-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="flex text-sm text-slate-600 justify-center">
                            <label class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 px-2 py-1">
                                <span>Upload a file</span>
                                <input type="file" name="screenshot" accept="image/*" class="sr-only">
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">Question Text (Optional)</label>
                <textarea name="question" rows="2" placeholder="Type here if you don't have a screenshot..." class="mt-1 block w-full rounded-md border-slate-300 shadow-sm p-2 border"></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">Solution (Leave blank for AI Auto-Solve)</label>
                <textarea name="solution" rows="2" placeholder="Leave this completely blank and the AI will generate the step-by-step solution..." class="mt-1 block w-full rounded-md border-slate-300 shadow-sm p-2 border bg-indigo-50/50"></textarea>
            </div>

            <div class="pt-4">
                <button type="submit" name="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 transition-colors">
                    Save & Generate Solution
                </button>
            </div>
        </form>
    </div>
	<script>
        const fileInput = document.querySelector('input[name="screenshot"]');
        const fileLabel = fileInput.previousElementSibling; // Targets the "Upload a file" span
        
        fileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                // Change the text to the filename and turn it green
                fileLabel.textContent = '✅ Attached: ' + this.files[0].name;
                fileLabel.classList.remove('text-indigo-600');
                fileLabel.classList.add('text-emerald-600', 'font-bold');
            }
        });
    </script>
</body>
</html>
