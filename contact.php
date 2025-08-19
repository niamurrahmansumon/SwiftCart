<?php
$pageTitle = "Contact Us - SwiftCart";
include 'includes/header.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);
    
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error = 'Please fill in all fields';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address';
    } else {
        // In a real application, you would send an email or save to database
        $success = 'Thank you for your message! We\'ll get back to you within 24 hours.';
        
        // Clear form data
        $_POST = [];
    }
}
?>

<main>
    <section class="hero" style="padding: 60px 0;">
        <div class="container">
            <h1>Contact Us</h1>
            <p>We're here to help! Get in touch with our team</p>
        </div>
    </section>
    
    <section style="padding: 60px 0;">
        <div class="container">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 60px; max-width: 1000px; margin: 0 auto;">
                <div>
                    <div style="background: white; padding: 40px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1);">
                        <h2 style="color: #667eea; margin-bottom: 30px;">Send us a Message</h2>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php endif; ?>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-error"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="form-group">
                                <label for="name">Full Name *</label>
                                <input type="text" id="name" name="name" required value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Email Address *</label>
                                <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="subject">Subject *</label>
                                <input type="text" id="subject" name="subject" required value="<?php echo htmlspecialchars($_POST['subject'] ?? ''); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="message">Message *</label>
                                <textarea id="message" name="message" rows="5" required><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                            </div>
                            
                            <button type="submit" class="btn">Send Message</button>
                        </form>
                    </div>
                </div>
                
                <div>
                    <div style="background: white; padding: 40px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); margin-bottom: 30px;">
                        <h3 style="color: #667eea; margin-bottom: 20px;">Get in Touch</h3>
                        
                        <div style="margin-bottom: 20px;">
                            <h4 style="color: #333; margin-bottom: 5px;">📧 Email</h4>
                            <p>support@swiftcart.com</p>
                        </div>
                        
                        <div style="margin-bottom: 20px;">
                            <h4 style="color: #333; margin-bottom: 5px;">📞 Phone</h4>
                            <p>+8801632311277</p>
                        </div>
                        
                        <div style="margin-bottom: 20px;">
                            <h4 style="color: #333; margin-bottom: 5px;">🕒 Business Hours</h4>
                            <p>Sunday - Thursday: 9:00 AM - 6:00 PM<br>
                            Saturday: 10:00 AM - 4:00 PM<br>
                            Friday: Closed</p>
                        </div>
                        
                        <div>
                            <h4 style="color: #333; margin-bottom: 5px;">📍 Address</h4>
                            <p>Daffodil Road<br>
                            Daffodil Smart City, DSC 5607<br>
                            Bangladesh</p>
                        </div>
                    </div>
                    
                    <div style="background: white; padding: 40px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1);">
                        <h3 style="color: #667eea; margin-bottom: 20px;">Frequently Asked Questions</h3>
                        
                        <div style="margin-bottom: 15px;">
                            <h4 style="color: #333; margin-bottom: 5px;">How long does shipping take?</h4>
                            <p style="font-size: 0.9rem; color: #666;">Standard shipping takes 3-5 business days. Express shipping is available for 1-2 day delivery.</p>
                        </div>
                        
                        <div style="margin-bottom: 15px;">
                            <h4 style="color: #333; margin-bottom: 5px;">What's your return policy?</h4>
                            <p style="font-size: 0.9rem; color: #666;">We offer a 30-day return policy for all unopened items in original packaging.</p>
                        </div>
                        
                        <div>
                            <h4 style="color: #333; margin-bottom: 5px;">Do you offer warranties?</h4>
                            <p style="font-size: 0.9rem; color: #666;">All products come with manufacturer warranties. Extended warranties are available for purchase.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
