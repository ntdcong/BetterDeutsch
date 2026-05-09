# BetterDeutsch 🇩🇪

**A High-Performance, Minimalist German Learning Platform built from scratch with Vanilla PHP.**

BetterDeutsch is a personal web application designed to help users learn German vocabulary and grammar efficiently. Built completely without heavy frameworks, it utilizes a custom-built MVC architecture, emphasizing raw performance, clean code, and a premium "Shadcn-inspired" user interface using Tailwind CSS. 

![BetterDeutsch Overview](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-00000F?style=for-the-badge&logo=mysql&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![Vanilla JS](https://img.shields.io/badge/Vanilla_JS-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)

## ✨ Core Features

### 📚 Intelligent Vocabulary Management
- **Notebooks & Groups:** Organize vocabulary into infinite notebooks and categorize them into logical groups.
- **Full CRUD System:** Add, edit, and delete vocabulary easily. Supports articles (der/die/das), plural forms, word types, and specific prepositions.
- **High-Performance Pagination & Search:** Instantly query thousands of words via server-side pagination and optimized SQL indexing.

### 🎴 Advanced Flashcard Engine
- **Immersive Learning:** A distraction-free, full-screen flashcard experience.
- **Dynamic CSS Animations:** Smooth hardware-accelerated transitions (3D card flips, deck-shuffling animations).
- **In-line Editing:** Spot a mistake while learning? Edit the vocabulary instantly without leaving the flashcard view.
- **Auto-Pronunciation:** Integrated with the Web Speech API to provide native-sounding German text-to-speech.

### 🔍 Built-in Verb Conjugator
- **Instant Lookup:** Query German verbs directly from the flashcards.
- **Comprehensive Tenses:** Displays perfectly aligned grids for *Präsens, Präteritum, Konjunktiv II, Perfekt,* and *Imperativ*.

### 🎨 Premium UI/UX Design
- **Shadcn-Inspired UI:** Modern, clean aesthetics featuring glassmorphism, subtle micro-interactions, and a carefully curated dark/light mode palette.
- **Mobile-First Responsive:** Flawless experience across desktops, tablets, and mobile devices.

## 🛠 Technical Architecture

BetterDeutsch was built to demonstrate a deep understanding of core web technologies and software design patterns:

- **Custom MVC Framework:** A fully bespoke router and MVC structure in PHP, ensuring absolute control over the request lifecycle without the overhead of frameworks like Laravel or Symfony.
- **Secure Authentication:** Implementation of secure session management, password hashing (Bcrypt), and CSRF protection.
- **Database Design:** Optimized MySQL relational schema utilizing `FOREIGN KEY` constraints (`ON DELETE CASCADE` / `SET NULL`) to maintain strict data integrity.
- **Frontend Optimization:** Uses native Vanilla JavaScript. No React or Vue overhead. DOM manipulation and state management are handled elegantly through custom event dispatchers.

## 🚀 Installation & Setup

1. **Clone the repository:**
   ```bash
   git clone https://github.com/yourusername/BetterDeutsch.git
   cd BetterDeutsch
   ```

2. **Database Configuration:**
   - Create a MySQL database (e.g., `betterdeutsch`).
   - Import the schema and seed data using `database_update.sql` and `verbs.sql`.
   - Update `config/database.php` with your local credentials.

3. **Run the Application:**
   ```bash
   # Using PHP's built-in server for development
   php -S localhost:8000 -t public
   ```

4. **Access the App:**
   Open `http://localhost:8000` in your browser.

## 📄 License

This project is intended for personal and educational use. Not licensed for commercial distribution.
