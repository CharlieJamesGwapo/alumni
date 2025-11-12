# Team Member Photos Guide

## 📸 Adding Your Team Photos

Place your team member photos in this directory with the following names:

- `member1.jpg` - Project Lead & Full Stack Developer
- `member2.jpg` - UI/UX Designer & Frontend Developer
- `member3.jpg` - Backend Developer & Database Architect
- `member4.jpg` - Quality Assurance & Documentation
- `member5.jpg` - DevOps & System Administrator
- `member6.jpg` - Content Manager & Support

## 📐 Recommended Image Specifications

- **Format**: JPG, PNG, or WebP
- **Dimensions**: 400x500px (portrait orientation) or square 400x400px
- **File Size**: Keep under 500KB for optimal loading
- **Quality**: High resolution, well-lit professional photos

## 🎨 Photo Tips

1. **Consistent Style**: Use photos with similar backgrounds or lighting for a cohesive look
2. **Professional**: Headshots or upper body shots work best
3. **High Quality**: Ensure photos are clear and not pixelated
4. **Centered**: Make sure faces are centered in the frame

## 🔄 Fallback System

Don't worry if you don't have photos yet! The system automatically generates beautiful avatar placeholders using the team member's name with your brand colors (blue gradient).

## ✏️ Customizing Team Members

To modify team member information, edit the `$team_members` array in `about.php` (around line 196):

```php
$team_members = [
  [
    'name' => 'Your Name',
    'role' => 'Your Role',
    'image' => 'assets/team/member1.jpg',
    'bio' => 'Your bio description here.',
    'social' => [
      'facebook' => 'https://facebook.com/yourprofile',
      'linkedin' => 'https://linkedin.com/in/yourprofile',
      'github' => 'https://github.com/yourusername'
    ]
  ],
  // Add more team members...
];
```

## 🌐 Social Media Links

Update the social media links in the array above. Set to `#` or remove if you don't want to display certain social icons.

---

**Need help?** The team section is fully responsive and will look great on all devices!
