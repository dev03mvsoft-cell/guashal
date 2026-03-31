# Gaushala - Modern Static Website

A premium, modern-classic static website for a cow sanctuary (Gaushala), built with a custom PHP router and high-end animation libraries.

## 🚀 Technologies Used

### 🏛️ Organization Information
*   **English**: Shri Gau Rakshak Seva Samiti (Panjarapol)
*   **Gujarati**: શ્રી ગૌ રક્ષક સેવા સમિતિ (પાંજરાપોળ)

### Frontend & Styling
*   **Tailwind CSS**: Utility-first CSS framework for modern, responsive UI.
*   **Google Fonts**: 'Outfit' for body and 'Playfair Display' for high-end headings.
*   **AOS (Animate On Scroll)**: For sleek reveal animations as the user scrolls.

### Animations & Interactivity
*   **GSAP (GreenSock Animation Platform)**: Professional-grade animations for hero entries and scroll triggers.
*   **Swiper.js**: Modern, touch-enabled slider for the hero and gallery sections.
*   **Vanilla JS**: Custom logic located in `asset/js/app.js`.

### Backend & Routing
*   **PHP**: Used for core routing and layout templating (`include/` system).
*   **Custom Array Router**: A simplified router in `index.php` that maps URLs to files in the `view/` folder.
*   **Apache .htaccess**: For clean URL handling (`RewriteEngine`).

---

## 🎨 Theme & Design System (UI/UX)

This project follows a **Modern-Classic Vedic Aesthetic** designed to inspire peace, reverence, and nature-centric values.

### 🌈 Color Palette
*   🟠 **Saffron (#FF6A00)**: Symbolizing Dharma, Energy, and the sacred flame.
*   🟡 **Gold (#FFD700)**: Promoting a Divine, premium, and illuminated feel.
*   ⚪ **White**: Ensuring a clean, peaceful, and spacious minimalist layout.
*   🌿 **Light Green / Sage**: Representing Nature, Ahimsa, and Gau Seva.

### 🧩 UI Style & UX
*   **Minimal + Premium**: Clean whitespace with high-quality imagery and high-end typography.
*   **Soft Shadows**: Using subtle depth for cards and sections for a light, floating feel.
*   **Rounded Cards**: 2xl and 3xl rounded corners for a gentle, approachable interface.
*   **Typography**: Using **'Playfair Display'** and **'Outfit'** with Sanskrit-inspired heading styles.

### 🕉️ Cultural Elements
*   **Sacred Icons**: Use of the **Om symbol**, Mandala patterns, and Temple silhouettes.
*   **Imagery**: Cow (Gau Mata) icons and cinematic photography of sanctuaries.

---

## 📂 Project Structure

```text
/Gaushala
│
├── index.php             # Core Router (Entry Point)
├── .htaccess             # Clean URL configuration
├── README.md             # Project Documentation
│
├── asset/                # Static Assets
│   ├── css/              # All CSS files (Strictly organized here)
│   ├── js/               # JavaScript logic (AOS, GSAP, Swiper init)
│   └── img/              # Images and branding
│
├── include/              # Reusable Layout Parts
│   ├── header.php        # Meta tags, CDNs, Navigation
│   └── footer.php        # Scripts, Footer links, GSAP init
│
└── view/                 # Page Content (Home, About, Contact, etc.)
```

---

## 🛠️ Strict Guidelines
*   **CSS Organization**: All custom styles **MUST** reside within the `asset/css/` directory. No inline styles or ad-hoc style tags in views.
*   **Routing**: New pages should be added to the `$routes` array in `index.php` and the corresponding file created in the `view/` folder.
*   **Modernity**: Always use Tailwind classes for layout and GSAP/AOS for a premium user experience.

---

## ⚡ How to Run
1. Ensure you have PHP installed.
2. Open your terminal in the root folder.
3. Run: `php -S localhost:8000`
4. Visit `http://localhost:8000` in your browser.
"# guashal" 
