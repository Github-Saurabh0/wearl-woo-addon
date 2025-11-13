#  Wearl Woo Addon - Post/page Update Date And Time

**Wearl Woo Addon** is a modern and lightweight **WooCommerce extension** developed by **[Wearl Technologies](https://wearl.co.in)**.  
It adds custom features, automation, and enhancements to your WordPress + WooCommerce store, built with scalability, clean structure, and secure coding practices.

---

##  Features

-  Custom WooCommerce hooks & filters  
-  Modular OOP-based architecture  
-  Easy integration with existing WooCommerce setup  
-  Admin settings panel (extendable)  
-  Secure data handling with sanitization  
-  Optimized for performance and compatibility  
-  Developer-friendly file structure  

---

##  Folder Structure

```
wearl-woo-addon/
│
├── wearl-woo-addon.php          # Main plugin bootstrap
├── readme.txt                   # WordPress.org readme (optional)
├── includes/                    # Core plugin logic
│   ├── class-activator.php
│   ├── class-deactivator.php
│   ├── class-core.php
│   ├── class-admin.php
│   ├── class-public.php
│   └── functions.php
│
├── admin/                       # Admin panel assets
│   ├── css/
│   ├── js/
│   └── partials/
│
├── public/                      # Frontend assets
│   ├── css/
│   ├── js/
│   └── partials/
│
└── assets/                      # Icons, images, banners
    ├── icon.png
    └── banner.jpg
```

---

##  Installation

### Option 1: Manual Upload
1. Download or clone the repository:
   ```bash
   git clone https://github.com/<your-username>/wearl-woo-addon.git
   ```
2. Upload the folder to `/wp-content/plugins/`
3. Activate **Wearl Woo Addon** in your WordPress Admin > Plugins

### Option 2: Direct Folder Setup (Local Dev)
1. Copy project to your local WordPress `wp-content/plugins` directory  
2. Run:
   ```bash
   php -S localhost:8080
   ```
3. Visit your WordPress admin dashboard → Plugins → Activate **Wearl Woo Addon**

---

##  How It Works

This addon follows a **modular class-based structure**, separating admin and public functionality:

- **`WWA_Core`** – Main plugin loader (handles hooks, init)
- **`WWA_Admin`** – Handles backend settings, menus, and admin scripts
- **`WWA_Public`** – Handles frontend styles, scripts, and WooCommerce customizations
- **`WWA_Activator` / `WWA_Deactivator`** – Handle activation/deactivation events

You can easily extend this with:
- Custom checkout fields  
- Product page modifications  
- REST API endpoints  
- Custom order status automation  

---

##  Developer Notes

- Requires **WordPress 6.0+**  
- Compatible with **WooCommerce 8.0+**  
- Tested on PHP **7.4+ / 8.2+**  
- Fully ready for version control and CI/CD deployment  

---

##  Example Hook

Here’s a sample hook that displays a message on the single product page:

```php
add_action('woocommerce_single_product_summary', function() {
    echo '<p style="color:#3c6;">✨ Thank you for visiting Wearl Store! ✨</p>';
}, 25);
```

---

##  Contact & Support

  Developed by [Wearl Technologies](https://wearl.co.in)  
 Email: [hello@wearl.co.in](mailto:hello@wearl.co.in)  
 Website: [https://wearl.co.in](https://wearl.co.in)  
 Instagram: [@dev.wearl](https://instagram.com/dev.wearl)

---

##  License

This project is licensed under the **GNU General Public License v2 (GPL2)**.  
You are free to modify and redistribute this software under the same license.

---

##  Contributing

Contributions, issues, and feature requests are welcome!  
1. Fork this repo  
2. Create a new branch: `git checkout -b feature/new-feature`  
3. Commit changes: `git commit -m "Added new feature"`  
4. Push and open a Pull Request  

---

##  About Wearl Technologies

**Wearl Technologies** is a digital innovation company specializing in:  
 •  Web Development 
 •  E-commerce Solutions 
 •  Mobile App Development 
 •  AI Integrations  

We help businesses **go digital and scale faster** through custom-built tech solutions.

 [Visit wearl.co.in →](https://wearl.co.in)

---

© 2025 [Wearl Technologies](https://wearl.co.in) – All Rights Reserved.
