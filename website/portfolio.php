<?php
require_once 'includes/config.php';
require_once 'includes/db_connection.php';

$pageTitle = "My Portfolio";

// Static portfolio data - you can customize this
$portfolio = [
    'full_name' => 'Your Name Here',
    'title' => 'Web Developer & Student',
    'bio' => 'Computer Science student with a passion for web development and creating functional, user-friendly applications. Experienced in full-stack development with PHP, JavaScript, and modern frameworks.',
    'skills' => ['PHP', 'MySQL', 'JavaScript', 'HTML5', 'CSS3', 'React', 'Node.js', 'Git', 'Laravel', 'Bootstrap'],
    'education' => "Bachelor of Science in Computer Science\nUniversity Name, 2020-2024\n\n• GPA: 3.8/4.0\n• Relevant Coursework: Web Development, Database Systems, Software Engineering\n• Senior Project: Academic Resource Portal Development",
    'experience' => "Web Development Intern\nTech Company, Summer 2023\n\n• Developed responsive web applications using PHP and JavaScript\n• Implemented database optimization techniques\n• Collaborated with team using Git version control\n\nFreelance Web Developer\n2021-Present\n\n• Built custom websites for small businesses\n• Provided maintenance and support services",
    'projects' => "Academic Resource Portal (Current)\n• Developed a comprehensive platform for sharing academic resources\n• Features: File upload/download system, user authentication, portfolio display\n• Technologies: PHP, MySQL, JavaScript, CSS\n\nE-commerce Website\n• Created a fully functional online store with shopping cart and payment integration\n• Implemented admin dashboard for product management\n• Technologies: Laravel, Stripe API, Bootstrap\n\nTask Management App\n• Built a collaborative task management application with real-time updates\n• Features: User roles, project tracking, notifications\n• Technologies: React, Node.js, Socket.io",
    'contact_email' => 'your.email@example.com',
    'github_url' => 'https://github.com/yourusername',
    'linkedin_url' => 'https://linkedin.com/in/yourprofile',
    'profile_image' => '' // Leave empty for default, or add path to your image
];

// Get file statistics if you want to show your contributions
$fileStats = [];
if(isset($_SESSION['user_id'])) {
    $filesQuery = $conn->query("SELECT file_type, COUNT(*) as count FROM files WHERE uploaded_by = {$_SESSION['user_id']} GROUP BY file_type");
    while($row = $filesQuery->fetch_assoc()) {
        $fileStats[$row['file_type']] = $row['count'];
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="portfolio-header">
    <div style="text-align: center;">
        <?php if(!empty($portfolio['profile_image'])): ?>
            <img src="<?php echo htmlspecialchars($portfolio['profile_image']); ?>" alt="Profile" class="profile-image">
        <?php else: ?>
            <div class="profile-image" style="background: linear-gradient(135deg, var(--primary), var(--secondary)); display: flex; align-items: center; justify-content: center; font-size: 3rem; color: white;">
                <?php echo strtoupper(substr($portfolio['full_name'], 0, 1)); ?>
            </div>
        <?php endif; ?>
        <h1><?php echo htmlspecialchars($portfolio['full_name']); ?></h1>
        <p style="color: var(--text-muted); font-size: 1.2rem;"><?php echo htmlspecialchars($portfolio['title']); ?></p>
        
        <?php if(!empty($portfolio['skills'])): ?>
            <div class="skills-list" style="justify-content: center; margin-top: 1rem;">
                <?php foreach($portfolio['skills'] as $skill): ?>
                    <span class="skill-tag"><?php echo trim($skill); ?></span>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="portfolio-content" style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; margin: 2rem 0;">
    <div>
        <?php if(!empty($portfolio['bio'])): ?>
            <div class="card" style="margin-bottom: 2rem;">
                <h3><i class="fas fa-user"></i> About Me</h3>
                <p style="line-height: 1.8;"><?php echo nl2br(htmlspecialchars($portfolio['bio'])); ?></p>
            </div>
        <?php endif; ?>

        <?php if(!empty($portfolio['education'])): ?>
            <div class="card" style="margin-bottom: 2rem;">
                <h3><i class="fas fa-graduation-cap"></i> Education</h3>
                <div style="padding: 1rem 0;">
                    <?php 
                    $educationLines = explode("\n", $portfolio['education']);
                    foreach($educationLines as $line):
                        if(empty(trim($line))) continue;
                        if(strpos($line, '•') === 0): ?>
                            <div style="margin-left: 1.5rem; margin-bottom: 0.5rem; color: var(--text-muted);">
                                <i class="fas fa-circle" style="font-size: 0.5rem; margin-right: 0.5rem;"></i>
                                <?php echo htmlspecialchars(substr($line, 2)); ?>
                            </div>
                        <?php else: ?>
                            <h4 style="color: var(--primary); margin: 1rem 0 0.5rem 0;"><?php echo htmlspecialchars($line); ?></h4>
                        <?php endif;
                    endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if(!empty($portfolio['experience'])): ?>
            <div class="card" style="margin-bottom: 2rem;">
                <h3><i class="fas fa-briefcase"></i> Experience</h3>
                <div style="padding: 1rem 0;">
                    <?php 
                    $experienceSections = explode("\n\n", $portfolio['experience']);
                    foreach($experienceSections as $section):
                        $lines = explode("\n", $section);
                        if(count($lines) > 0):
                    ?>
                        <div style="margin-bottom: 1.5rem;">
                            <h4 style="color: var(--primary); margin-bottom: 0.5rem;"><?php echo htmlspecialchars($lines[0]); ?></h4>
                            <?php if(isset($lines[1])): ?>
                                <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 0.5rem;"><?php echo htmlspecialchars($lines[1]); ?></p>
                            <?php endif; ?>
                            <ul style="margin-left: 1.5rem; color: var(--text-muted);">
                                <?php for($i = 2; $i < count($lines); $i++):
                                    if(!empty(trim($lines[$i])) && strpos($lines[$i], '•') === 0): ?>
                                        <li><?php echo htmlspecialchars(substr($lines[$i], 2)); ?></li>
                                    <?php endif;
                                endfor; ?>
                            </ul>
                        </div>
                    <?php endif;
                    endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if(!empty($portfolio['projects'])): ?>
            <div class="card">
                <h3><i class="fas fa-project-diagram"></i> Projects</h3>
                <div style="padding: 1rem 0;">
                    <?php 
                    $projectSections = explode("\n\n", $portfolio['projects']);
                    foreach($projectSections as $section):
                        $lines = explode("\n", $section);
                        if(count($lines) > 0):
                    ?>
                        <div style="margin-bottom: 1.5rem; padding: 1rem; background-color: rgba(255,255,255,0.03); border-radius: 8px;">
                            <h4 style="color: var(--secondary); margin-bottom: 0.5rem;"><?php echo htmlspecialchars($lines[0]); ?></h4>
                            <ul style="margin-left: 1.5rem; color: var(--text-muted);">
                                <?php for($i = 1; $i < count($lines); $i++):
                                    if(!empty(trim($lines[$i])) && strpos($lines[$i], '•') === 0): ?>
                                        <li><?php echo htmlspecialchars(substr($lines[$i], 2)); ?></li>
                                    <?php endif;
                                endfor; ?>
                            </ul>
                        </div>
                    <?php endif;
                    endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div>
        <?php if(!empty($fileStats)): ?>
            <div class="card" style="margin-bottom: 2rem;">
                <h3><i class="fas fa-chart-pie"></i> Platform Contributions</h3>
                <div style="margin-top: 1rem;">
                    <?php foreach(['handout', 'midterm', 'final', 'software', 'project'] as $type): ?>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; padding: 0.5rem; background-color: rgba(255,255,255,0.05); border-radius: 6px;">
                            <span><?php echo ucfirst($type); ?>s:</span>
                            <strong><?php echo $fileStats[$type] ?? 0; ?></strong>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="card" style="margin-bottom: 2rem;">
            <h3><i class="fas fa-link"></i> Contact & Links</h3>
            <div style="margin-top: 1rem;">
                <div style="margin-bottom: 1rem; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-envelope" style="color: var(--primary);"></i>
                    <div>
                        <strong>Email:</strong>
                        <p style="color: var(--text-muted); margin: 0;">
                            <a href="mailto:<?php echo htmlspecialchars($portfolio['contact_email']); ?>" style="color: var(--primary); text-decoration: none;">
                                <?php echo htmlspecialchars($portfolio['contact_email']); ?>
                            </a>
                        </p>
                    </div>
                </div>
                
                <?php if(!empty($portfolio['github_url'])): ?>
                <div style="margin-bottom: 1rem; display: flex; align-items: center; gap: 10px;">
                    <i class="fab fa-github" style="color: var(--primary);"></i>
                    <div>
                        <strong>GitHub:</strong>
                        <p style="margin: 0;">
                            <a href="<?php echo htmlspecialchars($portfolio['github_url']); ?>" target="_blank" style="color: var(--primary); text-decoration: none;">
                                <?php echo htmlspecialchars($portfolio['github_url']); ?>
                            </a>
                        </p>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if(!empty($portfolio['linkedin_url'])): ?>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <i class="fab fa-linkedin" style="color: var(--primary);"></i>
                    <div>
                        <strong>LinkedIn:</strong>
                        <p style="margin: 0;">
                            <a href="<?php echo htmlspecialchars($portfolio['linkedin_url']); ?>" target="_blank" style="color: var(--primary); text-decoration: none;">
                                <?php echo htmlspecialchars($portfolio['linkedin_url']); ?>
                            </a>
                        </p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card">
            <h3><i class="fas fa-download"></i> Download Resume</h3>
            <p style="color: var(--text-muted); margin: 1rem 0;">Download my complete resume for more detailed information.</p>
            <a href="uploads/resume.pdf" class="btn btn-primary" style="width: 100%; text-align: center;">
                <i class="fas fa-file-download"></i> Download Resume
            </a>
        </div>

        <div class="card" style="margin-top: 2rem;">
            <h3><i class="fas fa-code"></i> Technologies</h3>
            <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; margin-top: 1rem;">
                <span style="background-color: var(--primary); color: white; padding: 0.5rem 1rem; border-radius: 20px; font-size: 0.9rem;">PHP</span>
                <span style="background-color: var(--primary); color: white; padding: 0.5rem 1rem; border-radius: 20px; font-size: 0.9rem;">MySQL</span>
                <span style="background-color: var(--primary); color: white; padding: 0.5rem 1rem; border-radius: 20px; font-size: 0.9rem;">JavaScript</span>
                <span style="background-color: var(--primary); color: white; padding: 0.5rem 1rem; border-radius: 20px; font-size: 0.9rem;">HTML5</span>
                <span style="background-color: var(--primary); color: white; padding: 0.5rem 1rem; border-radius: 20px; font-size: 0.9rem;">CSS3</span>
                <span style="background-color: var(--primary); color: white; padding: 0.5rem 1rem; border-radius: 20px; font-size: 0.9rem;">React</span>
                <span style="background-color: var(--primary); color: white; padding: 0.5rem 1rem; border-radius: 20px; font-size: 0.9rem;">Git</span>
                <span style="background-color: var(--primary); color: white; padding: 0.5rem 1rem; border-radius: 20px; font-size: 0.9rem;">Docker</span>
            </div>
        </div>
    </div>
</div>

<div class="portfolio-footer" style="text-align: center; margin-top: 3rem; padding-top: 2rem; border-top: 1px solid var(--border);">
    <h3 style="color: var(--primary); margin-bottom: 1rem;">Let's Connect!</h3>
    <p style="color: var(--text-muted); max-width: 600px; margin: 0 auto 2rem;">
        I'm always open to discussing web development projects, new opportunities, or just chatting about technology.
    </p>
    <a href="mailto:<?php echo htmlspecialchars($portfolio['contact_email']); ?>" class="btn btn-primary" style="margin-right: 1rem;">
        <i class="fas fa-envelope"></i> Send Email
    </a>
    <a href="<?php echo htmlspecialchars($portfolio['linkedin_url']); ?>" target="_blank" class="btn btn-secondary">
        <i class="fab fa-linkedin"></i> Connect on LinkedIn
    </a>
</div>

<?php include 'includes/footer.php'; ?>