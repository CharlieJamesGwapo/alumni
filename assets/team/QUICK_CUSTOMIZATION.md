# ⚡ Quick Customization Cheat Sheet

## 🎯 Common Customizations

### 1️⃣ Change Team Section Title
**Location**: `about.php` line ~345

```php
<h2 class="display-4 font-weight-bold text-indigo-700">Meet Our Team</h2>
```

**Change to**:
```php
<h2 class="display-4 font-weight-bold text-indigo-700">Our Amazing Team</h2>
<!-- or -->
<h2 class="display-4 font-weight-bold text-indigo-700">The People Behind MOIST</h2>
<!-- or -->
<h2 class="display-4 font-weight-bold text-indigo-700">Meet the Developers</h2>
```

---

### 2️⃣ Change Subtitle
**Location**: `about.php` line ~346

```php
<p class="lead text-muted">The passionate individuals behind MOIST Alumni Management System</p>
```

**Change to**:
```php
<p class="lead text-muted">Dedicated professionals committed to excellence</p>
```

---

### 3️⃣ Change Gradient Colors
**Location**: `about.php` line ~56 (Overlay gradient)

**Current**:
```css
background: linear-gradient(135deg, rgba(37, 99, 235, 0.95), rgba(124, 58, 237, 0.95));
```

**Red to Orange**:
```css
background: linear-gradient(135deg, rgba(239, 68, 68, 0.95), rgba(249, 115, 22, 0.95));
```

**Green to Teal**:
```css
background: linear-gradient(135deg, rgba(16, 185, 129, 0.95), rgba(20, 184, 166, 0.95));
```

**Pink to Purple**:
```css
background: linear-gradient(135deg, rgba(236, 72, 153, 0.95), rgba(168, 85, 247, 0.95));
```

---

### 4️⃣ Change Card Hover Height
**Location**: `about.php` line ~29

**Current** (lifts 10px):
```css
.team-card:hover {
  transform: translateY(-10px);
}
```

**More dramatic** (lifts 20px):
```css
.team-card:hover {
  transform: translateY(-20px);
}
```

**Subtle** (lifts 5px):
```css
.team-card:hover {
  transform: translateY(-5px);
}
```

---

### 5️⃣ Change Image Zoom Level
**Location**: `about.php` line ~46

**Current** (110% zoom):
```css
.team-card:hover .team-img {
  transform: scale(1.1);
}
```

**More zoom** (120%):
```css
.team-card:hover .team-img {
  transform: scale(1.2);
}
```

**Less zoom** (105%):
```css
.team-card:hover .team-img {
  transform: scale(1.05);
}
```

---

### 6️⃣ Change Image Height
**Location**: `about.php` line ~35

**Current**:
```css
.team-img-wrapper {
  height: 320px;
}
```

**Taller**:
```css
.team-img-wrapper {
  height: 400px;
}
```

**Shorter**:
```css
.team-img-wrapper {
  height: 280px;
}
```

---

### 7️⃣ Change Card Border Radius
**Location**: `about.php` line ~24

**Current**:
```css
.team-card {
  border-radius: 12px;
}
```

**More rounded**:
```css
.team-card {
  border-radius: 20px;
}
```

**Sharp corners**:
```css
.team-card {
  border-radius: 0;
}
```

---

### 8️⃣ Change Social Icon Size
**Location**: `about.php` line ~73-75

**Current** (45px):
```css
.social-icon {
  width: 45px;
  height: 45px;
  font-size: 18px;
}
```

**Larger** (55px):
```css
.social-icon {
  width: 55px;
  height: 55px;
  font-size: 22px;
}
```

**Smaller** (35px):
```css
.social-icon {
  width: 35px;
  height: 35px;
  font-size: 14px;
}
```

---

### 9️⃣ Change Animation Speed
**Location**: `about.php` line ~147

**Current** (0.6s):
```css
.team-card {
  animation: fadeInUp 0.6s ease-out forwards;
}
```

**Faster** (0.3s):
```css
.team-card {
  animation: fadeInUp 0.3s ease-out forwards;
}
```

**Slower** (1s):
```css
.team-card {
  animation: fadeInUp 1s ease-out forwards;
}
```

---

### 🔟 Change Number of Columns

**Desktop (3 columns)**:
```html
<div class="col-lg-4 col-md-6 mb-4">
```

**Desktop 4 columns**:
```html
<div class="col-lg-3 col-md-6 mb-4">
```

**Desktop 2 columns**:
```html
<div class="col-lg-6 col-md-6 mb-4">
```

---

## 🎨 Color Reference

### Current Theme Colors
```css
Primary Blue:    #2563eb (rgb(37, 99, 235))
Primary Purple:  #7c3aed (rgb(124, 58, 237))
Indigo:          #4338ca (rgb(67, 56, 202))
```

### Alternative Color Schemes

**Professional Blue**:
```css
Primary:   #1e40af (Dark Blue)
Secondary: #3b82f6 (Light Blue)
```

**Modern Green**:
```css
Primary:   #059669 (Emerald)
Secondary: #10b981 (Green)
```

**Creative Orange**:
```css
Primary:   #ea580c (Orange)
Secondary: #f97316 (Light Orange)
```

**Elegant Purple**:
```css
Primary:   #7c3aed (Purple)
Secondary: #a855f7 (Light Purple)
```

---

## 📱 Responsive Breakpoints

```css
/* Mobile First */
Default:        < 576px  (1 column)
Small (sm):     ≥ 576px  (1 column)
Medium (md):    ≥ 768px  (2 columns)
Large (lg):     ≥ 992px  (3 columns)
Extra Large:    ≥ 1200px (3 columns)
```

---

## 🚀 Quick Tips

1. **Always backup** `about.php` before making changes
2. **Test on mobile** after any responsive changes
3. **Clear cache** (Ctrl+F5) to see CSS changes
4. **Use browser DevTools** to preview changes live
5. **Keep animations subtle** for professional look
6. **Maintain consistency** across all cards
7. **Optimize images** before uploading (compress to <500KB)
8. **Use web-safe fonts** or include font files

---

## 🔍 Finding Code Sections

| What to Change | Search For | Line # |
|----------------|------------|--------|
| Team Title | `Meet Our Team` | ~345 |
| Subtitle | `passionate individuals` | ~346 |
| Card Styles | `.team-card {` | ~22 |
| Image Height | `.team-img-wrapper {` | ~33 |
| Hover Effect | `.team-card:hover {` | ~28 |
| Gradient | `linear-gradient(135deg` | ~56 |
| Social Icons | `.social-icon {` | ~73 |
| Animations | `@keyframes fadeInUp` | ~135 |
| Team Data | `$team_members = [` | ~353 |

---

## 💡 Pro Customization Ideas

### Add Department Badges
```html
<span class="badge badge-primary">Development</span>
<span class="badge badge-success">Design</span>
```

### Add Skills Tags
```html
<div class="mt-2">
  <span class="badge badge-light">PHP</span>
  <span class="badge badge-light">MySQL</span>
</div>
```

### Add Contact Buttons
```html
<a href="mailto:email@example.com" class="btn btn-sm btn-outline-primary">
  <i class="fas fa-envelope"></i> Email
</a>
```

### Add Years of Experience
```html
<p class="text-muted small">
  <i class="fas fa-calendar-alt"></i> 5+ years experience
</p>
```

---

**Remember**: Small changes can have big impacts. Start with one customization at a time!
