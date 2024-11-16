index.php

<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Imtihon Portali</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .exam-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin: 10px;
            transition: transform 0.3s;
        }
        .exam-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .banner-carousel .item {
            height: 400px;
            background-size: cover;
            background-position: center;
        }
        .partner-logo {
            max-width: 150px;
            height: auto;
            margin: 20px;
        }
        .review-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin: 10px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="#">ExamPrep</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="#">Bosh Sahifa</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Imtihonlar</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Mock Testlar</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Kontakt</a></li>
                    <li class="nav-item"><a class="nav-link btn btn-primary text-white" href="#">Kirish</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Banner Carousel -->
    <div class="owl-carousel banner-carousel">
        <div class="item" style="background-image: url('/api/placeholder/1200/400');">
            <div class="container h-100">
                <div class="row h-100 align-items-center">
                    <div class="col-md-6">
                        <h1 class="text-white">IELTS Mock Tests</h1>
                        <p class="text-white">Professional darajada tayyorgarlik ko'ring</p>
                        <a href="#" class="btn btn-primary">Boshlash</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="item" style="background-image: url('/api/placeholder/1200/400');">
            <div class="container h-100">
                <div class="row h-100 align-items-center">
                    <div class="col-md-6">
                        <h1 class="text-white">TOEFL Imtihoniga Tayyorgarlik</h1>
                        <p class="text-white">Xalqaro tan olingan sertifikatga ega bo'ling</p>
                        <a href="#" class="btn btn-primary">Boshlash</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Exam Categories -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-4">Imtihon Kategoriyalari</h2>
            <div class="row">
                <div class="col-md-3">
                    <div class="exam-card text-center">
                        <i class="fas fa-graduation-cap fa-3x mb-3"></i>
                        <h4>IELTS</h4>
                        <p>Academic va General Training</p>
                        <a href="#" class="btn btn-outline-primary">Batafsil</a>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="exam-card text-center">
                        <i class="fas fa-book fa-3x mb-3"></i>
                        <h4>TOEFL</h4>
                        <p>Internet-based Test (iBT)</p>
                        <a href="#" class="btn btn-outline-primary">Batafsil</a>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="exam-card text-center">
                        <i class="fas fa-certificate fa-3x mb-3"></i>
                        <h4>LanguageCERT</h4>
                        <p>International ESOL</p>
                        <a href="#" class="btn btn-outline-primary">Batafsil</a>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="exam-card text-center">
                        <i class="fas fa-layer-group fa-3x mb-3"></i>
                        <h4>Multilevel</h4>
                        <p>Barcha darajalar uchun</p>
                        <a href="#" class="btn btn-outline-primary">Batafsil</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Latest Mock Tests -->
    <section class="bg-light py-5">
        <div class="container">
            <h2 class="text-center mb-4">So'nggi Mock Testlar</h2>
            <div class="owl-carousel mock-tests-carousel">
                <!-- Mock test items will be dynamically loaded -->
            </div>
        </div>
    </section>

    <!-- User Reviews -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-4">Foydalanuvchilar Fikrlari</h2>
            <div class="owl-carousel review-carousel">
                <div class="review-card">
                    <div class="d-flex align-items-center mb-3">
                        <img src="/api/placeholder/50/50" class="rounded-circle me-3" alt="User">
                        <div>
                            <h5 class="mb-0">Aziz Rahimov</h5>
                            <small class="text-muted">IELTS: Band 7.5</small>
                        </div>
                    </div>
                    <p>"Bu platforma orqali IELTS imtihoniga tayyorgarlik ko'rdim va kutganimdan ham yaxshi natija oldim."</p>
                </div>
                <!-- More review items -->
            </div>
        </div>
    </section>

    <!-- Partners -->
    <section class="bg-light py-5">
        <div class="container">
            <h2 class="text-center mb-4">Hamkorlarimiz</h2>
            <div class="row justify-content-center align-items-center">
                <div class="col-md-2 text-center">
                    <img src="/api/placeholder/150/80" alt="Partner 1" class="partner-logo">
                </div>
                <!-- More partner logos -->
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>ExamPrep</h5>
                    <p>Professional imtihonga tayyorgarlik portali</p>
                </div>
                <div class="col-md-4">
                    <h5>Tezkor Havolalar</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-light">Imtihonlar</a></li>
                        <li><a href="#" class="text-light">Mock Testlar</a></li>
                        <li><a href="#" class="text-light">Blog</a></li>
                        <li><a href="#" class="text-light">FAQ</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Bog'lanish</h5>
                    <p>Email: info@examprep.uz</p>
                    <p>Tel: +998 99 123 45 67</p>
                    <div class="social-links">
                        <a href="#" class="text-light me-2"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="text-light me-2"><i class="fab fa-telegram"></i></a>
                        <a href="#" class="text-light me-2"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
    <script>
        $(document).ready(function(){
            // Banner carousel initialization
            $('.banner-carousel').owlCarousel({
                items: 1,
                loop: true,
                autoplay: true,
                autoplayTimeout: 5000,
                autoplayHoverPause: true,
                nav: true,
                dots: true
            });

            // Mock tests carousel initialization
            $('.mock-tests-carousel').owlCarousel({
                loop: true,
                margin: 20,
                nav: true,
                responsive: {
                    0: { items: 1 },
                    768: { items: 2 },
                    992: { items: 3 }
                }
            });

            // Reviews carousel initialization
            $('.review-carousel').owlCarousel({
                loop: true,
                margin: 20,
                nav: true,
                responsive: {
                    0: { items: 1 },
                    768: { items: 2 },
                    992: { items: 3 }
                }
            });
        });
    </script>
</body>
</html>

____________________________________________________________________________________________________________________________________________



