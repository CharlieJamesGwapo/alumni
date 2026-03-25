<?php
/**
 * Team Members Template
 * 
 * Copy this template to quickly add new team members to your about.php page.
 * Simply fill in the details and paste into the $team_members array.
 */

// TEMPLATE - Copy and customize this block for each team member
[
  'name' => 'Full Name',
  'role' => 'Job Title / Position',
  'image' => 'assets/team/memberX.jpg',  // Change X to your member number
  'bio' => 'A brief 2-3 sentence description highlighting expertise and contributions.',
  'social' => [
    'facebook' => 'https://facebook.com/username',  // Or '#' to hide
    'linkedin' => 'https://linkedin.com/in/username',  // Or '#' to hide
    'github' => 'https://github.com/username'  // Or '#' to hide
  ]
],

// EXAMPLE 1: Full Stack Developer
[
  'name' => 'Alex Rivera',
  'role' => 'Senior Full Stack Developer',
  'image' => 'assets/team/member7.jpg',
  'bio' => 'Passionate about building scalable web applications with modern technologies. 5+ years experience in PHP, JavaScript, and cloud architecture.',
  'social' => [
    'facebook' => 'https://facebook.com/alexrivera',
    'linkedin' => 'https://linkedin.com/in/alexrivera',
    'github' => 'https://github.com/alexrivera'
  ]
],

// EXAMPLE 2: Marketing Specialist (No GitHub)
[
  'name' => 'Maria Santos',
  'role' => 'Digital Marketing Specialist',
  'image' => 'assets/team/member8.jpg',
  'bio' => 'Creating engaging content and driving growth through strategic digital campaigns. Expert in SEO, social media, and analytics.',
  'social' => [
    'facebook' => 'https://facebook.com/mariasantos',
    'linkedin' => 'https://linkedin.com/in/mariasantos',
    'github' => '#'  // Hidden - not applicable for this role
  ]
],

// EXAMPLE 3: Minimal Social Presence
[
  'name' => 'James Chen',
  'role' => 'Data Analyst',
  'image' => 'assets/team/member9.jpg',
  'bio' => 'Transforming data into actionable insights. Specialized in business intelligence and predictive analytics.',
  'social' => [
    'facebook' => '#',
    'linkedin' => 'https://linkedin.com/in/jameschen',
    'github' => '#'
  ]
],

/**
 * QUICK REFERENCE
 * 
 * Recommended Image Sizes:
 * - Portrait: 400x500px
 * - Square: 400x400px
 * - Max file size: 500KB
 * 
 * Bio Guidelines:
 * - Keep it 2-3 sentences
 * - Highlight key expertise
 * - Mention years of experience (optional)
 * - Keep it professional yet personable
 * 
 * Social Media:
 * - Use full URLs (https://...)
 * - Set to '#' to hide icon
 * - Icons appear on hover
 * - Opens in new tab
 * 
 * Adding to about.php:
 * 1. Open about.php
 * 2. Find the $team_members array (around line 353)
 * 3. Copy your customized block from above
 * 4. Paste it into the array
 * 5. Make sure to add a comma after each block
 * 6. Save and refresh the page!
 */
?>
