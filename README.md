# ⚡ Error-Notebook: AI-Powered Exam Prep & Spaced Repetition Tracker

![PHP](https://img.shields.io/badge/PHP-8.x-777BB4?style=flat-square&logo=php&logoColor=white)
![Database](https://img.shields.io/badge/Database-MySQL-4479A1?style=flat-square&logo=mysql&logoColor=white)
![AI Engine](https://img.shields.io/badge/AI%20Engine-Google%20Gemini-orange?style=flat-square&logo=google)
![Frontend](https://img.shields.io/badge/UI-Tailwind%20CSS-06B6D4?style=flat-square&logo=tailwindcss&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green?style=flat-square)

An intelligent revision and mistake-tracking system designed to log, categorize, and schedule competitive exam questions (SSC, CDS, Banking) using spaced repetition. The application integrates **Google Gemini API** to automatically analyze uploaded question screenshots and generate step-by-step mathematical and conceptual solutions[cite: 23, 25].

---

## 📌 Project Overview

The **Error-Notebook** solves a critical problem in competitive examination preparation: efficiently logging and relearning from mistakes made during timed mock tests. Instead of maintaining manual notebooks or unorganized screenshot folders, this platform automates concept tagging, step-by-step problem resolution, and revision scheduling[cite: 23, 27].

By pairing multimodal generative AI with dynamic spaced repetition scheduling, the system acts as an autonomous tutor and memory retention engine across four key subjects[cite: 23, 25]:

1. **Quantitative Aptitude & Mathematics 📐** (Detailed derivations, trigonometric identities, geometric proofs)
2. **Reasoning & General Intelligence 🧩** (Syllogisms, directional sense, pattern recognition)
3. **General Studies & Sciences 🔬** (Physics laws, historical facts, factual explanations)
4. **English Language & Comprehension 📖** (Grammar rule derivations, vocabulary contextualization)

---

## 📁 Repository Structure & Directory Layout

Below is the file organization and architecture of the application:

```text
Error-Notebook/
├── uploads/                    # Local storage directory for uploaded mock exam screenshots
├── add_error.php               # Question logger with Gemini multimodal vision integration
├── all_mistakes.php            # Complete chronological history view and question bank
├── database.sql                # Relational schema definition for database setup
├── db.php                      # Database connection handler with local timezone configuration
├── index.php                   # Daily spaced repetition review dashboard
├── process_review.php          # Interval calculation logic for review responses
└── README.md                   # Master project documentation
## 🛠️ Key Technical Capabilities

* **Multimodal Vision Problem Solving:** Sends Base64-encoded image payloads directly to Google's Gemini endpoint via PHP cURL, reading question diagrams, mathematical formulas, and text automatically.


* **Automated Categorization & Concept Extraction:** Uses system-instructed prompt constraints and JSON response schemas to extract problem sub-categories (e.g., *Trigonometry*, *Calculation Error*, *Forgot Formula*) alongside the solution.


* **Dynamic Spaced Repetition Engine:** Implements an interval-doubling algorithm. When a student marks a problem as "Got it!", the review interval scales ($1 \to 2 \to 4 \to 8 \to 16\text{ days}$). Marking "Forgot" resets the cycle to 1 day for prompt reinforcement.


* **Strict Schema Grounding:** Enforces native JSON generation configs (`responseMimeType: application/json`) to ensure AI outputs parse reliably without formatting corruption.


* **Responsive Dashboard Interface:** Built with clean Tailwind CSS utility classes, featuring expandable HTML `<details>` disclosure panels for zero-clutter solution reviews and active question counters.



---

## 🗄️ Relational Database Schema (`mistakes`)

The application persists all review cycles and question metadata inside a single optimized MySQL table:

```sql
CREATE TABLE mistakes (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    subject VARCHAR(100) NOT NULL,
    question TEXT,
    error_type VARCHAR(50) NOT NULL,
    solution TEXT NOT NULL,
    interval_days INT(11) DEFAULT 1,
    next_review DATE NOT NULL,
    image_path VARCHAR(255) DEFAULT NULL
);

```

### Schema Data Dictionary

| Column | Type | Description |
| --- | --- | --- |
| `id` | `INT(11)` | Auto-incrementing primary key identifier. |
| `subject` | `VARCHAR(100)` | Academic domain (`Maths`, `Reasoning`, `General Science`, etc.).

 |
| `question` | `TEXT` | Extracted problem text, manual context notes, or prompt statements.

 |
| `error_type` | `VARCHAR(50)` | AI-detected topic classification or manual error reason.

 |
| `solution` | `TEXT` | AI-generated or user-provided step-by-step explanation.

 |
| `interval_days` | `INT(11)` | Current repetition multiplier in days.

 |
| `next_review` | `DATE` | Scheduled date when the item reappears on the dashboard.

 |
| `image_path` | `VARCHAR(255)` | Filename reference to the saved screenshot in `/uploads`.

 |

---

## 💻 Tech Stack

* **Core AI Engine:** Google Gemini API (`gemini-2.0-flash` / `gemini-3.5-flash-lite`)


* **Backend Logic:** PHP 8.x


* **Database Layer:** MySQL 8.x / MariaDB


* **Frontend Interface:** Tailwind CSS (via CDN) & Vanilla JavaScript


* **Local Server Environment:** Laragon / XAMPP / WampServer



---

## 🚀 Quick Start & Installation

To run this project locally, follow these steps:

### 1. Clone the Repository

```bash
git clone [https://github.com/niketsah007/Error-Notebook.git](https://github.com/niketsah007/Error-Notebook.git)
cd Error-Notebook

```

### 2. Configure the Database

1. Open your MySQL client (HeidiSQL, phpMyAdmin, or MySQL CLI).
2. Create a database named `my_exam_prep`:


```sql
CREATE DATABASE my_exam_prep;

```


3. Import the `database.sql` script into the newly created database.



### 3. Create Storage Directory

Create an `uploads` directory inside the project root if it does not already exist:

```bash
mkdir uploads

```

*(Ensure your web server has write permissions to this directory).*

### 4. Configure Database Credentials & API Key

* Open `db.php` and verify your local database settings:


```php
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "my_exam_prep";

```


* Open `add_error.php`, locate line 5, and insert your Google AI Studio API key:


```php
$GEMINI_API_KEY = "YOUR_ACTUAL_API_KEY";

```



### 5. Access the Application

Start your local server (Laragon/XAMPP) and open your browser[cite: 29]:

```text
http://localhost/error_notebook/index.php

```

*(Or `http://error_notebook.test/index.php` if using Laragon virtual hosts).*

---

## 📈 Future Roadmap

* 📊 **Subject-Wise Error Analytics:** Visual heatmaps showing topics with the highest mistake density.
* 🗂️ **Tag & Filter System:** Filter questions across specific mock providers or exam types.
* 📱 **Anki Card Export:** Direct sync or `.apkg` export for mobile flashcard review.
* ✍️ **LaTeX Math Rendering:** Integration with MathJax or KaTeX for formatted equation rendering.

---

## 📄 License

This project is licensed under the **MIT License**. See the `LICENSE` file for full details.

---

## 👤 Author

**Niket Sah**

* GitHub: [@niketsah007](https://www.google.com/search?q=https://github.com/niketsah007)

* B.Tech Computer Science Engineering

```

```
