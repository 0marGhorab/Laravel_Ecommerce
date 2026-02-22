# E-Commerce Project – Status & Roadmap

## What Has Been Done

### 1. Project Setup & Foundation
- Laravel project with Livewire 3 and Tailwind CSS
- MySQL database configuration
- MVC structure and clean code practices
- Laravel Breeze for authentication (login, register, password reset)

### 2. Database & Models
- **Migrations**: users, categories, products, product_images, carts, cart_items, wishlists, wishlist_items, addresses, orders, order_items
- **Models** with relationships: Category, Product, ProductImage, Cart, CartItem, Wishlist, WishlistItem, Address, Order, OrderItem, User
- **Indexes** for performance: composite indexes on carts, cart_items, products, product_images, orders, order_items, wishlists, wishlist_items
- **Seeders**: dummy categories (16), products with images (12 products, 30 images)

### 3. Product Catalog
- **Products index** (`/`): grid, category filter, search
- **Product show** (`/products/{slug}`): detail page, image carousel, thumbnails
- **Category bar**: horizontal carousel with left/right arrows, category filtering
- **Search**: navbar search with dropdown, product images in results
- **Category filtering**: filter products by category with URL sync

### 4. Shopping Cart
- Add to cart from product index and product detail
- Quantity controls (+/-) on cards and detail page
- Cart page: list items, update quantity, remove, subtotal
- Cart counter in navbar (updates via Livewire events)
- Cart cached per request for performance

### 5. Wishlist
- Add/remove from wishlist on product index and product detail
- Wishlist page with list and “Add to cart” from wishlist
- Guest users: login prompt on wishlist page (no redirect)
- Wishlist counter in navbar
- Eager loading to avoid N+1 queries

### 6. Checkout
- Shipping and billing address forms (with validation)
- Phone with country code dropdown, numeric-only input
- Postal code validation by country
- Saved addresses dropdown with auto-fill
- “Use a new address” clears fields
- Shipping methods (free over $100, standard)
- Order summary (subtotal, tax, shipping, total)
- Validation on blur and on “Complete Order”
- Order creation with unique order number
- Redirect to products page + “Order placed successfully” modal
- Cart cleared after order

### 7. Orders
- **Order history** (`/orders`): list of user orders with status, date, total, item count, thumbnails
- **Order details** (`/orders/{orderNumber}`): full order, items, addresses, totals, payment status
- Links in user dropdown and mobile menu

### 8. Authentication & Navigation
- Sign In / Sign Up in navbar for guests (desktop and mobile)
- User dropdown when logged in: My Orders, Profile, Log Out
- Login redirects to **products page** (not dashboard)
- Toast “Logged in successfully” after login
- Dashboard page with quick links (Browse Products, View Orders)

### 9. UI/UX
- Toast notifications: product added to cart, wishlist add/remove, login required
- Order success modal (auto-close after 2 seconds)
- Product card image carousel (multiple images, arrows, dots)
- Responsive layout
- Consistent styling (Tailwind, indigo/gray theme)

### 10. Performance
- Cart cached per request (`Cart::current()`)
- Category list cached (1 hour)
- Database indexes on hot paths
- Wishlist queries with eager loading
- Cart cache cleared after cart updates

### 11. Admin Board
- **Access**: `/admin` (auth + `is_admin` required). “Admin” link in user dropdown and mobile menu for admin users.
- **Dashboard**: Stats (orders, products, users, revenue), recent orders table.
- **Orders**: List with search (order #, customer), status filter; detail page with items, addresses, totals; update status (pending → processing → shipped → delivered / cancelled).
- **Products**: List with search (name, SKU), status filter (active/draft/archived).
- **Users**: List with search (name, email); admin badge shown.
- **Layout**: Sidebar nav (Dashboard, Orders, Products, Users, View Store), header with user and log out.
- **Making an admin**: Set `is_admin = 1` for a user (e.g. `User::where('email', 'you@example.com')->update(['is_admin' => true]);` in tinker, or run a one-off migration/seeder).

---

## Next Steps (Suggested Order)

1. **Payment integration** – Stripe or PayPal for real payments (currently order is created without payment flow).
2. **Admin panel** – ~~Manage products, categories, orders, users (CRUD, status updates).~~ Done: dashboard, orders list/detail/status, products list, users list. Optional next: product CRUD, category CRUD.
3. **Email notifications** – Order confirmation, status updates (done); password reset (Breeze has reset).
4. **Enhancing UI** – Polish and consistency (see “Enhancing UI” section below). *Partially done: loading states, empty states, search feedback, focus-visible.*
5. **Order tracking** – Status timeline, tracking number, “Track order” on order detail.
6. **Combine address queries in checkout** – ~~Eager load shipping/billing addresses in `CheckoutPage`.~~ Done: one query, split by type.

---

## What Is Left for the Project

### High Priority
| Task | Description | Status |
|------|-------------|--------|
| Payment integration | Stripe Checkout for card payments | Done (redirect flow; success/cancel routes; config in `config/services.php`) |
| Admin panel | Products, orders, categories, users management | Done (CRUD for products/categories; order status + tracking) |
| Order emails | Order confirmation and status update emails | Done (confirmation on place order; status email when admin updates) |

### Medium Priority
| Task | Description | Status |
|------|-------------|--------|
| Order tracking | Tracking number, shipped_at, timeline on order detail, admin set tracking | Done |
| Product reviews & ratings | Customer reviews and star rating on product page | Done (star rating, list, submit, one per user) |
| Coupon / discount codes | Apply discount at checkout | Done (percentage/fixed, min order, max uses, per user) |
| Enhancing UI | Consistency, loading states, empty states, accessibility | Partially done (loading, empty states, search, focus) |
| Combine address queries | Eager load addresses in checkout | Done (single query, split by type) |

### Lower Priority
| Task | Description | Status |
|------|-------------|--------|
| Customer account dashboard | Saved addresses, profile, order history in one place | Partially (orders + profile exist) |
| Inventory management | Stock checks, low stock warnings, backorders | Not started |
| Invoice download | PDF invoice for orders | Not started |
| Reorder | “Buy again” from order history | Not started |
| Query logging / Debugbar | Dev-only query logging to find slow queries | Not started |
| Lazy loading products | Infinite scroll or “Load more” (optional) | Not started |

---

## Enhancing UI (Missing Tasks)

These are the UI improvements to add to the “what is left” list:

### Global
- [x] **Loading states**: Spinners/disabled state for Livewire actions (add to cart, quantity, wishlist, place order, search).
- [x] **Empty states**: Consistent empty-state component for cart, wishlist, orders, product list, checkout empty cart (icon + message + CTA).
- [ ] **Error states**: Clear, user-friendly messages for validation and server errors; retry where useful.
- [x] **Focus & accessibility**: Visible focus-visible ring on links and buttons (keyboard users).
- [ ] **Responsive polish**: Test and fix layout/overflow on very small and very large screens.

### Products & Catalog
- [ ] **Product cards**: Hover effects, consistent spacing, better typography hierarchy.
- [ ] **Category bar**: Clear active state, better contrast, optional mobile “drawer” for categories.
- [ ] **Product detail**: Clear section hierarchy, sticky “Add to cart” on scroll (mobile), breadcrumbs.
- [x] **Search**: Loading indicator while searching, “No results” state with icon and suggestion.

### Cart & Checkout
- [x] **Cart page**: Clearer “Empty cart” state and CTA to shop (empty-state component).
- [ ] **Checkout**: Progress indicator (e.g. Address → Shipping → Payment → Review), clearer grouping of fields.
- [ ] **Forms**: Consistent label/error styling, optional inline hints for postal code/phone formats.

### Orders & Account
- [ ] **Order list**: Status pills/badges (already present), clearer date and total, better mobile layout.
- [ ] **Order detail**: Clear sections (items, addresses, totals), print-friendly styles.
- [ ] **Dashboard**: Simple stats (e.g. recent orders, quick links), clearer “Welcome” block.

### Notifications & Feedback
- [ ] **Toasts**: Consistent position, duration, and “undo” where applicable (e.g. remove from cart).
- [ ] **Modals**: Consistent padding, close button, and behavior (e.g. order success).
- [x] **Buttons**: Loading state (Complete Order, Place Order, Add to cart, quantity, search) to prevent double submit and give feedback.

### Visual Consistency
- [ ] **Colors**: Single source for primary/secondary (e.g. Tailwind config or CSS variables).
- [ ] **Spacing**: Consistent padding/margins (e.g. 4, 6, 8 scale).
- [ ] **Typography**: Consistent heading sizes and weights across pages.
- [ ] **Icons**: Same icon set and sizes (e.g. Heroicons) across navbar, cards, buttons.

---

## Quick Reference – Main Routes

| Route | Purpose |
|-------|--------|
| `/` | Products index (with category filter) |
| `/products/{slug}` | Product detail |
| `/cart` | Cart page |
| `/checkout` | Checkout |
| `/wishlist` | Wishlist |
| `/orders` | Order history (auth) |
| `/orders/{orderNumber}` | Order detail (auth) |
| `/dashboard` | Dashboard (auth) |
| `/profile` | Profile (auth) |
| `/login`, `/register` | Auth (Breeze) |

---

## Files to Know

- **Livewire**: `app/Livewire/` (ProductIndex, ProductShow, CartPage, WishlistPage, CheckoutPage, OrderHistoryPage, OrderShowPage).
- **Views**: `resources/views/livewire/`, `resources/views/components/layouts/app.blade.php`, `resources/views/livewire/layout/navigation.blade.php`.
- **Models**: `app/Models/` (Cart, Product, Order, Category, etc.).
- **Performance**: `app/Models/Cart.php` (caching), `database/migrations/2026_02_10_130000_add_indexes_to_tables.php`, `PERFORMANCE_OPTIMIZATIONS.md`.

---

*Last updated: Feb 2026*
