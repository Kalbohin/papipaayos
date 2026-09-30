<?php
include("./config/config.php");
include("./helpers/SystemOperators.php");

$con = connection();
$so = new SystemOperators();

// all the choices in the dropdown menu for contact us
$subjects = [
    "Computer & Laptop Repair",
    "Software Installation",
    "Virus/Malware Removal",
    "PC Cleaning & Optimization",
    "Product Inquiry",
    "Other",
];

$errors = [];
$old = ["name" => "", "email" => "", "phone" => "", "subject" => "", "message" => ""];
$sent = isset($_GET["sent"]);

//print text inside HTML
function e($value)
{
    return htmlspecialchars($value, ENT_QUOTES, "UTF-8");
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["btnSubmit"])) {
    // get and clean the submitted values
    foreach ($old as $field => $value) {
        $old[$field] = trim($_POST[$field] ?? "");
    }

    //This is the condition if the inputed by the user are correct on each type box or empty, if the condition for correct met. The information inputted wont go to the database
    if ($old["name"] === "" || strlen($old["name"]) > 100) {
        $errors[] = "Please enter your name.";
    }
    if (!filter_var($old["email"], FILTER_VALIDATE_EMAIL) || strlen($old["email"]) > 150) {
        $errors[] = "Please enter a valid email address.";
    }
    if (!preg_match('/^[0-9+\-\s()]{7,20}$/', $old["phone"])) {
        $errors[] = "Please enter a valid phone number.";
    }
    if (!in_array($old["subject"], $subjects, true)) {
        $errors[] = "Please choose a subject from the list.";
    }
    if ($old["message"] === "" || strlen($old["message"]) > 2000) {
        $errors[] = "Please describe your concern.";
    }

    //if the information given is valid i=on the form, then all the information goes straight t the database sql
    if (empty($errors)) {
        $enc_name = $so->encrypt(trim(filter_input(INPUT_POST, "name", FILTER_SANITIZE_SPECIAL_CHARS)));
        $enc_email = $so->encrypt(trim(filter_input(INPUT_POST, "email", FILTER_SANITIZE_SPECIAL_CHARS)));
        $enc_phone = $so->encrypt(trim(filter_input(INPUT_POST, "phone", FILTER_SANITIZE_SPECIAL_CHARS)));
        $enc_subject = $so->encrypt(trim(filter_input(INPUT_POST, "subject", FILTER_SANITIZE_SPECIAL_CHARS)));
        $enc_message = $so->encrypt(trim(filter_input(INPUT_POST, "message", FILTER_SANITIZE_SPECIAL_CHARS)));

        $insert_query = "INSERT INTO `contact_messages` (`name`, `email`, `phone`, `subject`, `message`) VALUES (?, ?, ?, ?, ?)";
        $insert_stmt = $con->prepare($insert_query);
        $insert_stmt->bind_param("sssss", $enc_name, $enc_email, $enc_phone, $enc_subject, $enc_message);

        try {
            if ($insert_stmt->execute()) {
                $insert_stmt->close();
                // Redirect so refreshing the page does not send the form twice
                header("Location: index.php?sent=1#contact-form");
                exit();
            } else {
                $errors[] = "Sorry, your request could not be saved. Please try again.";
            }
        } catch (mysqli_sql_exception $e) {
            $errors[] = "Sorry, your request could not be saved. Please try again.";
        }
    }
}
// true right after the form was submitted (success message or error messages are showing)
$afterSubmit = $sent || !empty($errors);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Papi Paayos</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
        <link rel="stylesheet" href="css/style.css" />
    </head>
    <body>
        <!-- NAVBAR -->
        <nav class="navbar navbar-expand-lg bg-white shadow sticky-top p-2">
            <div class="container-fluid">
                <!-- Only the navbar title uses the Might font (class "site-title") -->
                <a class="navbar-brand site-title ps-4" href="#home">
                    Papi Paayos
                </a>
                <!--For mobile navbar(uses hamburger button)-->
                <button class="navbar-toggler"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#navbarNav"
                        aria-controls="navbarNav"
                        aria-expanded="false"
                        aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="#home">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#services">Services</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#products">Products</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#about">About Us</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#contact">Contact Us</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- HOME -->
        <section id="home" class="hero">
            <div class="container">
                <div class="row align-items-center g-5">
                    <div class="col-lg-6">
                        <h1>Just say "Papi Paayos", and we'll fix it for you!</h1>
                        <p class="lead mt-3">
                            Papi Paayos is an IT repair shop for desktops and laptops.
                            Tell us what is wrong through the contact form and we will get back to you
                            with a plan to fix it.
                        </p>
                        <div class="d-flex flex-wrap gap-3 mt-4">
                            <a href="#services" class="btn btn-tech btn-lg">View Services</a>
                            <a href="#contact" class="btn btn-ghost btn-lg">Request a Repair</a>
                        </div>
                        <ul class="hero-points">
                            <li>Desktop and laptop repair</li>
                            <li>Software installs and virus removal</li>
                            <li>Keyboards, SSDs, and other parts in stock</li>
                        </ul>
                    </div>
                    <div class="col-lg-6">
                        <div class="device">
                            <div class="device-bar" aria-hidden="true">
                                <span></span><span></span><span></span>
                            </div>
                            <div class="ratio ratio-4x3">
                                <img src="img/fixingcomputer.jpg" class="img-fluid w-100" alt="Technician repairing a laptop">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- SERVICES -->
        <section id="services" class="section-pad section-mist">
            <div class="container">
                <h2>Our Services</h2>
                <p class="section-intro mt-2 mb-5">
                    From a laptop that will not turn on to a PC that has slowed down over the years,
                    here is what we can help with.
                </p>
                <div class="row g-4">
                    <div class="col-md-6 col-lg-3">
                        <div class="service-card">
                            <div class="service-icon" aria-hidden="true">💻</div>
                            <h3>Computer &amp; Laptop Repair</h3>
                            <p>We diagnose and fix hardware problems like no power, broken screens, overheating, and etc.</p>
                            <a href="#contact" class="link-tech">Book this service</a>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="service-card accent-2">
                            <div class="service-icon" aria-hidden="true">🛠️</div>
                            <h3>Software Installation</h3>
                            <p>Windows installs, drivers, office apps, and the programs you need, set up and ready to use.</p>
                            <a href="#contact" class="link-tech">Book this service</a>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="service-card accent-3">
                            <div class="service-icon" aria-hidden="true">🦠</div>
                            <h3>Virus/Malware Removal</h3>
                            <p>We find and remove viruses, pop-ups, and unwanted programs, then help protect your files.</p>
                            <a href="#contact" class="link-tech">Book this service</a>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="service-card accent-4">
                            <div class="service-icon" aria-hidden="true">⚡</div>
                            <h3>PC Cleaning &amp; Optimization</h3>
                            <p>Dust cleaning, fresh thermal paste, and software tune-ups to make a slow PC fast again.</p>
                            <a href="#contact" class="link-tech">Book this service</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- PRODUCTS -->
        <section id="products" class="section-pad">
            <div class="container">
                <h2>Products</h2>
                <p class="section-intro mt-2 mb-5">
                    Everyday accessories and upgrades we sell in the shop. Message us to reserve an item.
                </p>
                <div class="row g-4">
                    <div class="col-sm-6 col-lg-3">
                        <div class="product-card">
                            <div class="ratio ratio-1x1">
                                <img src="img/mouse.jpg" class="img-fluid w-100 h-100" alt="Wireless Mouse">
                            </div>
                            <div class="card-body">
                                <h3>Wireless Mouse</h3>
                                <p>Comfortable, cable-free mouse for work and everyday browsing.</p>
                                <a href="#contact" class="link-tech">Ask about this item</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="product-card">
                            <div class="ratio ratio-1x1">
                                <img src="img/keyboard.jpg" class="img-fluid w-100 h-100" alt="Wireless Mouse">
                            </div>
                            <div class="card-body">
                                <h3>Mechanical Keyboard</h3>
                                <p>Clicky, durable keys that feel great for typing and gaming.</p>
                                <a href="#contact" class="link-tech">Ask about this item</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="product-card">
                            <div class="ratio ratio-1x1">
                                <img src="img/headset.jpg" class="img-fluid w-100 h-100" alt="Wireless Mouse">
                            </div>
                            <div class="card-body">
                                <h3>Gaming Headset</h3>
                                <p>Listen to the audio of your games in high quality.</p>
                                <a href="#contact" class="link-tech">Ask about this item</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="product-card">
                            <div class="ratio ratio-1x1">
                                <img src="img/monitor.jpg" class="img-fluid w-100 h-100" alt="Wireless Mouse">
                            </div>
                            <div class="card-body">
                                <h3>Gaming Monitor</h3>
                                <p>Keeps a high resolution and high quality for you to enjoy the game.</p>
                                <a href="#contact" class="link-tech">Ask about this item</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ABOUT US -->
        <section id="about" class="section-pad section-mist">
            <div class="container">
                <div class="row align-items-center g-5">
                    <div class="col-lg-5">
                        <div class="about-photo">
                            <div class="ratio ratio-1x1">
                                <img src="img/owner.jpg" class="img-fluid" alt="Owner of Papi Paayos">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <h2>About Us</h2>
                        <p class="mt-3">
                            Hi I am <strong>Benedict Paloma</strong>, the owner and developer behind Papi Paayos website.
                            I sought wo make this on purpose to have a service website for technical services like repairing,
                            diagnostics, cleaning and malware removal for desktops or laptop. While also having a variety of
                            available peripherals for computers.
                        </p>
                        <ul class="about-list">
                            <li>Honest diagnosis before any repair starts</li>
                            <li>Plain explanations</li>
                            <li>Requests handled through one simple form</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- CONTACTS US -->
        <section id="contact" class="section-pad contact-section">
            <div class="container">
                <div class="row g-5">
                    <div class="col-lg-5">
                        <h2>Contact Us</h2>
                        <p class="section-intro mt-2">
                            Need a repair or have a question? Fill out the form and describe what is going on with your
                            desktop or laptop.
                        </p>
                        <h3 class="h5 mt-4">How it works</h3>
                        <ol class="steps">
                            <li>Send your request using the form.</li>
                            <li>We contact you to confirm the details.</li>
                            <li>You bring in your device, or we agree on a schedule.</li>
                        </ol>
                        <ul class="contact-details">
                            <li>Email: benedictpaloma46@email.com</li>
                            <li>Phone: 0961 004 4886</li>
                            <li>Address: Greenhills</li>
                        </ul>
                    </div>
                    <div class="col-lg-7">
                        <div class="form-card">
                            <?php if ($sent): ?>
                                <div class="alert alert-success" role="alert">
                                    Thank you! Your request was sent. We will contact you soon.
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($errors)): ?>
                                <div class="alert alert-danger" role="alert">
                                    <strong>Please fix the following:</strong>
                                    <ul class="mb-0 mt-1">
                                        <?php foreach ($errors as $error): ?>
                                            <li><?php echo e($error); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                            <form action="#contact" method="post" novalidate>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="name" class="form-label">Full name</label>
                                        <input type="text" class="form-control" id="name" name="name"
                                               maxlength="100" autocomplete="name" required
                                               value="<?php echo e($old["name"]); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email"
                                               maxlength="150" autocomplete="email" required
                                               value="<?php echo e($old["email"]); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="phone" class="form-label">Phone number</label>
                                        <input type="tel" class="form-control" id="phone" name="phone"
                                               maxlength="20" autocomplete="tel" required
                                               value="<?php echo e($old["phone"]); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="subject" class="form-label">Subject</label>
                                        <select class="form-select" id="subject" name="subject" required>
                                            <option value="">Choose a service...</option>
                                            <?php foreach ($subjects as $subject): ?>
                                                <option value="<?php echo e($subject); ?>"
                                                    <?php echo $old["subject"] === $subject ? "selected" : ""; ?>>
                                                    <?php echo e($subject); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label for="message" class="form-label">Message</label>
                                        <textarea class="form-control" id="message" name="message" rows="5"
                                                  maxlength="2000" required
                                                  placeholder="Describe the problem, the device brand/model, and any error messages."><?php echo e(
                                                      $old["message"]
                                                  ); ?></textarea>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" name="btnSubmit" href="contact" class="btn btn-tech btn-lg w-100">Send request</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- FOOTER -->
        <footer class="site-footer">
            <div class="container text-center">
                &copy; <?php echo date("Y"); ?> Papi Paayos. All rights reserved.
            </div>
        </footer>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    </body>
</html>