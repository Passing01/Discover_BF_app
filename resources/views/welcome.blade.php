@extends('layouts.landing')

@section('content')

    <!-- Hero Section -->
    <section id="hero" class="hero section hero-overlay dark-background">
      <div class="hero-bg-img"></div>
      <div class="container" data-aos="fade-up" data-aos-delay="150">
        <div class="row justify-content-center">
          <div class="col-xl-9 col-lg-10 text-center">
            <h1 class="display-4 fw-bold text-white mb-3">
              DISCOVER<span class="accent">.</span> EXPLORE<span class="accent">.</span> EXPERIENCE<span class="accent">.</span>
            </h1>
            <p class="lead text-white-50 mb-4">Partez à la découverte des plus beaux sites et activités au Burkina Faso.</p>

            <form class="hero-search d-grid g-2 g-md-3 align-items-center" action="{{ route('sites.index') }}" method="GET">
              <div class="input-wrap">
                <i class="bi bi-geo-alt"></i>
                <input type="text" name="q" class="form-control" placeholder="Où allez-vous ?">
              </div>
              <div class="input-wrap">
                <i class="bi bi-calendar3"></i>
                <input type="date" name="from" class="form-control" placeholder="Date d'arrivée">
              </div>
              <div class="input-wrap">
                <i class="bi bi-calendar3"></i>
                <input type="date" name="to" class="form-control" placeholder="Date de départ">
              </div>
              <div class="input-wrap">
                <i class="bi bi-people"></i>
                <input type="number" min="1" name="guests" class="form-control" placeholder="Voyageurs">
              </div>
              <div class="d-grid">
                <button class="btn btn-primary btn-lg rounded-3 shadow-sm" type="submit">
                  <i class="bi bi-search"></i> Rechercher
                </button>
              </div>
            </form>

            <div class="hero-chips">
              <a href="{{ route('sites.index') }}" class="chip"><i class="bi bi-sun"></i> Plages</a>
              <a href="{{ route('events.index') }}" class="chip"><i class="bi bi-music-note-beamed"></i> Évènements</a>
              <a href="{{ route('tourist.hotels.index') }}" class="chip"><i class="bi bi-building"></i> Hébergements</a>
              <a href="{{ route('transport.taxi.index') }}" class="chip"><i class="bi bi-car-front"></i> Transport</a>
            </div>
          </div>
        </div>
      </div>
    </section><!-- /Hero Section -->

    <!-- Highlights: Activités populaires -->
    <section class="section py-5">
      <div class="container">
        <div class="d-flex align-items-end justify-content-between mb-4">
          <div>
            <h2 class="h3 mb-1">Activités populaires</h2>
            <p class="text-muted mb-0">Des expériences inoubliables à portée de clic.</p>
          </div>
          <a href="{{ route('sites.index') }}" class="btn btn-outline-primary btn-sm">Voir tout</a>
        </div>
        <div class="row g-4">
          <div class="col-md-6 col-lg-3">
            <div class="card card-spot h-100">
              <img src="{{ asset('assets/img/services-1.jpg') }}" class="card-img-top" alt="">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <span class="badge bg-warning text-dark">Randonnée</span>
                  <small class="text-muted"><i class="bi bi-star-fill text-warning"></i> 4.9</small>
                </div>
                <h3 class="h6 mb-2">Balades à travers les collines</h3>
                <p class="card-text text-muted small mb-0">Guides locaux et sentiers sécurisés</p>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-lg-3">
            <div class="card card-spot h-100">
              <img src="{{ asset('assets/img/services-2.jpg') }}" class="card-img-top" alt="">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <span class="badge bg-info text-dark">Culture</span>
                  <small class="text-muted"><i class="bi bi-star-fill text-warning"></i> 4.8</small>
                </div>
                <h3 class="h6 mb-2">Visites de musées</h3>
                <p class="card-text text-muted small mb-0">Patrimoine et traditions</p>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-lg-3">
            <div class="card card-spot h-100">
              <img src="{{ asset('assets/img/services-3.jpg') }}" class="card-img-top" alt="">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <span class="badge bg-success">Nature</span>
                  <small class="text-muted"><i class="bi bi-star-fill text-warning"></i> 5.0</small>
                </div>
                <h3 class="h6 mb-2">Parcs et réserves</h3>
                <p class="card-text text-muted small mb-0">Faune et flore préservées</p>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-lg-3">
            <div class="card card-spot h-100">
              <img src="{{ asset('assets/img/working-1.jpg') }}" class="card-img-top" alt="">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <span class="badge bg-danger">Aventure</span>
                  <small class="text-muted"><i class="bi bi-star-fill text-warning"></i> 4.7</small>
                </div>
                <h3 class="h6 mb-2">Excursions en 4x4</h3>
                <p class="card-text text-muted small mb-0">Adrénaline garantie</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Destinations populaires -->
    <section class="section pt-0 pb-5">
      <div class="container">
        <div class="d-flex align-items-end justify-content-between mb-4">
          <div>
            <h2 class="h3 mb-1">Destinations populaires</h2>
            <p class="text-muted mb-0">Choisies pour vous.</p>
          </div>
          <a href="{{ route('tourist.hotels.index') }}" class="btn btn-outline-primary btn-sm">Explorer</a>
        </div>
        <div class="row g-4">
          <div class="col-sm-6 col-lg-3">
            <div class="card card-place h-100">
              <img class="card-img-top" src="{{ asset('assets/img/working-2.jpg') }}" alt="">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                  <h3 class="h6 mb-0">Lac de Tengréla</h3>
                  <span class="price">à partir de 25 000 CFA</span>
                </div>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-lg-3">
            <div class="card card-place h-100">
              <img class="card-img-top" src="{{ asset('assets/img/working-3.jpg') }}" alt="">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                  <h3 class="h6 mb-0">Banfora Cascades</h3>
                  <span class="price">à partir de 30 000 CFA</span>
                </div>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-lg-3">
            <div class="card card-place h-100">
              <img class="card-img-top" src="{{ asset('assets/img/working-4.jpg') }}" alt="">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                  <h3 class="h6 mb-0">Koudougou</h3>
                  <span class="price">à partir de 18 000 CFA</span>
                </div>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-lg-3">
            <div class="card card-place h-100">
              <img class="card-img-top" src="{{ asset('assets/img/services.jpg') }}" alt="">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                  <h3 class="h6 mb-0">Ouagadougou</h3>
                  <span class="price">à partir de 20 000 CFA</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    @push('styles')
    <style>
      /* Hero background image */
      #hero { position: relative; min-height: 85vh; display: grid; align-items: center; }
      #hero .hero-bg-img { position:absolute; inset:0; background:url('{{ asset('assets/img/hero-bg.jpg') }}') center/cover no-repeat; filter: saturate(105%) contrast(102%); z-index:0; }
      #hero.hero-overlay::before { content:""; position:absolute; inset:0; background:linear-gradient(180deg, rgba(0,0,0,.45), rgba(0,0,0,.15)); z-index:0; }
      #hero .container { position:relative; z-index:1; }
      #hero .accent{ color:#ff7e5f; }

      /* Floating white search card */
      .hero-search { grid-template-columns: 1.2fr repeat(3, 1fr) auto; gap:16px; padding:16px; background:#ffffff; border:1px solid rgba(0,0,0,.06); border-radius:16px; box-shadow:0 20px 50px rgba(0,0,0,.18); max-width:1100px; margin:0 auto; }
      @media (min-width: 992px){ .hero-search{ margin-top:18px; } }
      @media (max-width: 991.98px){ .hero-search{ grid-template-columns: 1fr 1fr; } }
      @media (max-width: 575.98px){ .hero-search{ grid-template-columns: 1fr; } }
      .hero-search .input-wrap{ position:relative; }
      .hero-search .input-wrap i{ position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#6c757d; }
      .hero-search .form-control{ padding-left:38px; height:56px; border-radius:12px; }
      .hero-search .btn{ height:56px; font-weight:700; border-radius:12px; }

      .hero-chips{ margin-top:16px; display:flex; flex-wrap:wrap; gap:8px; justify-content:center; }
      .chip{ display:inline-flex; align-items:center; gap:6px; padding:8px 12px; border-radius:999px; background:#fff; color:#212529; border:1px solid rgba(0,0,0,.06); box-shadow:0 4px 12px rgba(0,0,0,.06); font-weight:600; }
      .chip:hover{ text-decoration:none; opacity:.9; }

      /* Cards */
      .card-spot, .card-place{ border:1px solid rgba(0,0,0,.06); box-shadow:0 8px 24px rgba(0,0,0,.06); border-radius:16px; overflow:hidden; }
      .card-spot img, .card-place img{ height:170px; object-fit:cover; }
      .card-place .price{ font-weight:600; color:#ff7e5f; }

      /* Section headings closer to mock */
      .section .h3, .section h2.h3{ font-weight:800; letter-spacing:.2px; }
      .section p.text-muted{ color:#6b7280 !important; }
    </style>
    @endpush

    @push('scripts')
    <script>
      // small UX: if "to" date is before "from", sync it
      document.addEventListener('DOMContentLoaded', function(){
        var from = document.querySelector('form.hero-search input[name=from]');
        var to = document.querySelector('form.hero-search input[name=to]');
        if(from && to){
          from.addEventListener('change', function(){ if(to.value && to.value < from.value) to.value = from.value; });
        }
      });
    </script>
    @endpush

    <!-- About Section -->
    <section id="about" class="about section">

      <div class="container">

        <div class="row gy-4">
          <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
            <h3>Voluptatem dignissimos provident laboris nisi ut aliquip ex ea commodo</h3>
            <img src="{{ asset('assets/img/about.jpg') }}" class="img-fluid rounded-4 mb-4" alt="">
            <p>Ut fugiat ut sunt quia veniam. Voluptate perferendis perspiciatis quod nisi et. Placeat debitis quia recusandae odit et consequatur voluptatem. Dignissimos pariatur consectetur fugiat voluptas ea.</p>
            <p>Temporibus nihil enim deserunt sed ea. Provident sit expedita aut cupiditate nihil vitae quo officia vel. Blanditiis eligendi possimus et in cum. Quidem eos ut sint rem veniam qui. Ut ut repellendus nobis tempore doloribus debitis explicabo similique sit. Accusantium sed ut omnis beatae neque deleniti repellendus.</p>
          </div>
          <div class="col-lg-6" data-aos="fade-up" data-aos-delay="250">
            <div class="content ps-0 ps-lg-5">
              <p class="fst-italic">
                Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore
                magna aliqua.
              </p>
              <ul>
                <li><i class="bi bi-check-circle-fill"></i> <span>Ullamco laboris nisi ut aliquip ex ea commodo consequat.</span></li>
                <li><i class="bi bi-check-circle-fill"></i> <span>Duis aute irure dolor in reprehenderit in voluptate velit.</span></li>
                <li><i class="bi bi-check-circle-fill"></i> <span>Ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate trideta storacalaperda mastiro dolore eu fugiat nulla pariatur.</span></li>
              </ul>
              <p>
                Ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate
                velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident
              </p>

              <div class="position-relative mt-4">
                <img src="{{ asset('assets/img/about-2.jpg') }}" class="img-fluid rounded-4" alt="">
                <a href="https://www.youtube.com/watch?v=Y7f98aduVJ8" class="glightbox pulsating-play-btn"></a>
              </div>
            </div>
          </div>
        </div>

      </div>

    </section><!-- /About Section -->

    <!-- Stats Section -->
    <section id="stats" class="stats section light-background">

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row gy-4">

          <div class="col-lg-3 col-md-6">
            <div class="stats-item d-flex align-items-center w-100 h-100">
              <i class="bi bi-emoji-smile color-blue flex-shrink-0"></i>
              <div>
                <span data-purecounter-start="0" data-purecounter-end="232" data-purecounter-duration="1" class="purecounter"></span>
                <p>Happy Clients</p>
              </div>
            </div>
          </div><!-- End Stats Item -->

          <div class="col-lg-3 col-md-6">
            <div class="stats-item d-flex align-items-center w-100 h-100">
              <i class="bi bi-journal-richtext color-orange flex-shrink-0"></i>
              <div>
                <span data-purecounter-start="0" data-purecounter-end="521" data-purecounter-duration="1" class="purecounter"></span>
                <p>Projects</p>
              </div>
            </div>
          </div><!-- End Stats Item -->

          <div class="col-lg-3 col-md-6">
            <div class="stats-item d-flex align-items-center w-100 h-100">
              <i class="bi bi-headset color-green flex-shrink-0"></i>
              <div>
                <span data-purecounter-start="0" data-purecounter-end="1463" data-purecounter-duration="1" class="purecounter"></span>
                <p>Hours Of Support</p>
              </div>
            </div>
          </div><!-- End Stats Item -->

          <div class="col-lg-3 col-md-6">
            <div class="stats-item d-flex align-items-center w-100 h-100">
              <i class="bi bi-people color-pink flex-shrink-0"></i>
              <div>
                <span data-purecounter-start="0" data-purecounter-end="15" data-purecounter-duration="1" class="purecounter"></span>
                <p>Hard Workers</p>
              </div>
            </div>
          </div><!-- End Stats Item -->

        </div>

      </div>

    </section><!-- /Stats Section -->

    <!-- Services Section -->
    <section id="services" class="services section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Services</h2>
        <p>Featured Srvices<br></p>
      </div><!-- End Section Title -->

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row gy-5">

          <div class="col-xl-4 col-md-6" data-aos="zoom-in" data-aos-delay="200">
            <div class="service-item">
              <div class="img">
                <img src="{{ asset('assets/img/services-1.jpg') }}" class="img-fluid" alt="">
              </div>
              <div class="details position-relative">
                <div class="icon">
                  <i class="bi bi-activity"></i>
                </div>
                <a href="service-details.html" class="stretched-link">
                  <h3>Nesciunt Mete</h3>
                </a>
                <p>Provident nihil minus qui consequatur non omnis maiores. Eos accusantium minus dolores iure perferendis.</p>
              </div>
            </div>
          </div><!-- End Service Item -->

          <div class="col-xl-4 col-md-6" data-aos="zoom-in" data-aos-delay="300">
            <div class="service-item">
              <div class="img">
                <img src="{{ asset('assets/img/services-2.jpg') }}" class="img-fluid" alt="">
              </div>
              <div class="details position-relative">
                <div class="icon">
                  <i class="bi bi-broadcast"></i>
                </div>
                <a href="service-details.html" class="stretched-link">
                  <h3>Eosle Commodi</h3>
                </a>
                <p>Ut autem aut autem non a. Sint sint sit facilis nam iusto sint. Libero corrupti neque eum hic non ut nesciunt dolorem.</p>
              </div>
            </div>
          </div><!-- End Service Item -->

          <div class="col-xl-4 col-md-6" data-aos="zoom-in" data-aos-delay="400">
            <div class="service-item">
              <div class="img">
                <img src="{{ asset('assets/img/services-3.jpg') }}" class="img-fluid" alt="">
              </div>
              <div class="details position-relative">
                <div class="icon">
                  <i class="bi bi-easel"></i>
                </div>
                <a href="service-details.html" class="stretched-link">
                  <h3>Ledo Markt</h3>
                </a>
                <p>Ut excepturi voluptatem nisi sed. Quidem fuga consequatur. Minus ea aut. Vel qui id voluptas adipisci eos earum corrupti.</p>
              </div>
            </div>
          </div><!-- End Service Item -->

        </div>

      </div>

    </section><!-- /Services Section -->

    <!-- Clients Section -->
    <section id="clients" class="clients section light-background">

      <div class="container" data-aos="fade-up">

        <div class="row gy-4">

          <div class="col-xl-2 col-md-3 col-6 client-logo">
            <img src="{{ asset('assets/img/clients/client-1.png') }}" class="img-fluid" alt="">
          </div><!-- End Client Item -->

          <div class="col-xl-2 col-md-3 col-6 client-logo">
            <img src="{{ asset('assets/img/clients/client-2.png') }}" class="img-fluid" alt="">
          </div><!-- End Client Item -->

          <div class="col-xl-2 col-md-3 col-6 client-logo">
            <img src="{{ asset('assets/img/clients/client-3.png') }}" class="img-fluid" alt="">
          </div><!-- End Client Item -->

          <div class="col-xl-2 col-md-3 col-6 client-logo">
            <img src="{{ asset('assets/img/clients/client-4.png') }}" class="img-fluid" alt="">
          </div><!-- End Client Item -->

          <div class="col-xl-2 col-md-3 col-6 client-logo">
            <img src="{{ asset('assets/img/clients/client-5.png') }}" class="img-fluid" alt="">
          </div><!-- End Client Item -->

          <div class="col-xl-2 col-md-3 col-6 client-logo">
            <img src="{{ asset('assets/img/clients/client-6.png') }}" class="img-fluid" alt="">
          </div><!-- End Client Item -->

        </div>

      </div>

    </section><!-- /Clients Section -->

    <!-- Features Section -->
    <section id="features" class="features section">

      <div class="container">

        <ul class="nav nav-tabs row  d-flex" data-aos="fade-up" data-aos-delay="100">
          <li class="nav-item col-3">
            <a class="nav-link active show" data-bs-toggle="tab" data-bs-target="#features-tab-1">
              <i class="bi bi-binoculars"></i>
              <h4 class="d-none d-lg-block">Modi sit est dela pireda nest</h4>
            </a>
          </li>
          <li class="nav-item col-3">
            <a class="nav-link" data-bs-toggle="tab" data-bs-target="#features-tab-2">
              <i class="bi bi-box-seam"></i>
              <h4 class="d-none d-lg-block">Unde praesenti mara setra le</h4>
            </a>
          </li>
          <li class="nav-item col-3">
            <a class="nav-link" data-bs-toggle="tab" data-bs-target="#features-tab-3">
              <i class="bi bi-brightness-high"></i>
              <h4 class="d-none d-lg-block">Pariatur explica nitro dela</h4>
            </a>
          </li>
          <li class="nav-item col-3">
            <a class="nav-link" data-bs-toggle="tab" data-bs-target="#features-tab-4">
              <i class="bi bi-command"></i>
              <h4 class="d-none d-lg-block">Nostrum qui dile node</h4>
            </a>
          </li>
        </ul><!-- End Tab Nav -->

        <div class="tab-content" data-aos="fade-up" data-aos-delay="200">

          <div class="tab-pane fade active show" id="features-tab-1">
            <div class="row">
              <div class="col-lg-6 order-2 order-lg-1 mt-3 mt-lg-0">
                <h3>Voluptatem dignissimos provident quasi corporis voluptates sit assumenda.</h3>
                <p class="fst-italic">
                  Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore
                  magna aliqua.
                </p>
                <ul>
                  <li><i class="bi bi-check2-all"></i>
                    <spab>Ullamco laboris nisi ut aliquip ex ea commodo consequat.</spab>
                  </li>
                  <li><i class="bi bi-check2-all"></i> <span>Duis aute irure dolor in reprehenderit in voluptate velit</span>.</li>
                  <li><i class="bi bi-check2-all"></i> <span>Ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate trideta storacalaperda mastiro dolore eu fugiat nulla pariatur.</span></li>
                </ul>
                <p>
                  Ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate
                  velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in
                  culpa qui officia deserunt mollit anim id est laborum
                </p>
              </div>
              <div class="col-lg-6 order-1 order-lg-2 text-center">
                <img src="{{ asset('assets/img/working-1.jpg') }}" alt="" class="img-fluid">
              </div>
            </div>
          </div><!-- End Tab Content Item -->

          <div class="tab-pane fade" id="features-tab-2">
            <div class="row">
              <div class="col-lg-6 order-2 order-lg-1 mt-3 mt-lg-0">
                <h3>Neque exercitationem debitis soluta quos debitis quo mollitia officia est</h3>
                <p>
                  Ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate
                  velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in
                  culpa qui officia deserunt mollit anim id est laborum
                </p>
                <p class="fst-italic">
                  Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore
                  magna aliqua.
                </p>
                <ul>
                  <li><i class="bi bi-check2-all"></i> <span>Ullamco laboris nisi ut aliquip ex ea commodo consequat.</span></li>
                  <li><i class="bi bi-check2-all"></i> <span>Duis aute irure dolor in reprehenderit in voluptate velit.</span></li>
                  <li><i class="bi bi-check2-all"></i> <span>Provident mollitia neque rerum asperiores dolores quos qui a. Ipsum neque dolor voluptate nisi sed.</span></li>
                  <li><i class="bi bi-check2-all"></i> <span>Ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate trideta storacalaperda mastiro dolore eu fugiat nulla pariatur.</span></li>
                </ul>
              </div>
              <div class="col-lg-6 order-1 order-lg-2 text-center">
                <img src="{{ asset('assets/img/working-2.jpg') }}" alt="" class="img-fluid">
              </div>
            </div>
          </div><!-- End Tab Content Item -->

          <div class="tab-pane fade" id="features-tab-3">
            <div class="row">
              <div class="col-lg-6 order-2 order-lg-1 mt-3 mt-lg-0">
                <h3>Voluptatibus commodi ut accusamus ea repudiandae ut autem dolor ut assumenda</h3>
                <p>
                  Ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate
                  velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in
                  culpa qui officia deserunt mollit anim id est laborum
                </p>
                <ul>
                  <li><i class="bi bi-check2-all"></i> <span>Ullamco laboris nisi ut aliquip ex ea commodo consequat.</span></li>
                  <li><i class="bi bi-check2-all"></i> <span>Duis aute irure dolor in reprehenderit in voluptate velit.</span></li>
                  <li><i class="bi bi-check2-all"></i> <span>Provident mollitia neque rerum asperiores dolores quos qui a. Ipsum neque dolor voluptate nisi sed.</span></li>
                </ul>
                <p class="fst-italic">
                  Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore
                  magna aliqua.
                </p>
              </div>
              <div class="col-lg-6 order-1 order-lg-2 text-center">
                <img src="{{ asset('assets/img/working-3.jpg') }}" alt="" class="img-fluid">
              </div>
            </div>
          </div><!-- End Tab Content Item -->

          <div class="tab-pane fade" id="features-tab-4">
            <div class="row">
              <div class="col-lg-6 order-2 order-lg-1 mt-3 mt-lg-0">
                <h3>Omnis fugiat ea explicabo sunt dolorum asperiores sequi inventore rerum</h3>
                <p>
                  Ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate
                  velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in
                  culpa qui officia deserunt mollit anim id est laborum
                </p>
                <p class="fst-italic">
                  Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore
                  magna aliqua.
                </p>
                <ul>
                  <li><i class="bi bi-check2-all"></i> <span>Ullamco laboris nisi ut aliquip ex ea commodo consequat.</span></li>
                  <li><i class="bi bi-check2-all"></i> <span>Duis aute irure dolor in reprehenderit in voluptate velit.</span></li>
                  <li><i class="bi bi-check2-all"></i> <span>Ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate trideta storacalaperda mastiro dolore eu fugiat nulla pariatur.</span></li>
                </ul>
              </div>
              <div class="col-lg-6 order-1 order-lg-2 text-center">
                <img src="{{ asset('assets/img/working-4.jpg') }}" alt="" class="img-fluid">
              </div>
            </div>
          </div><!-- End Tab Content Item -->

        </div>

      </div>

    </section><!-- /Features Section -->

    <!-- Services 2 Section -->
    <section id="services-2" class="services-2 section light-background">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Services</h2>
        <p>CHECK OUR SERVICES</p>
      </div><!-- End Section Title -->

      <div class="container">

        <div class="row gy-4">

          <div class="col-md-6" data-aos="fade-up" data-aos-delay="100">
            <div class="service-item d-flex position-relative h-100">
              <i class="bi bi-briefcase icon flex-shrink-0"></i>
              <div>
                <h4 class="title"><a href="#" class="stretched-link">Lorem Ipsum</a></h4>
                <p class="description">Voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident</p>
              </div>
            </div>
          </div><!-- End Service Item -->

          <div class="col-md-6" data-aos="fade-up" data-aos-delay="200">
            <div class="service-item d-flex position-relative h-100">
              <i class="bi bi-card-checklist icon flex-shrink-0"></i>
              <div>
                <h4 class="title"><a href="#" class="stretched-link">Dolor Sitema</a></h4>
                <p class="description">Minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat tarad limino ata</p>
              </div>
            </div>
          </div><!-- End Service Item -->

          <div class="col-md-6" data-aos="fade-up" data-aos-delay="300">
            <div class="service-item d-flex position-relative h-100">
              <i class="bi bi-bar-chart icon flex-shrink-0"></i>
              <div>
                <h4 class="title"><a href="#" class="stretched-link">Sed ut perspiciatis</a></h4>
                <p class="description">Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur</p>
              </div>
            </div>
          </div><!-- End Service Item -->

          <div class="col-md-6" data-aos="fade-up" data-aos-delay="400">
            <div class="service-item d-flex position-relative h-100">
              <i class="bi bi-binoculars icon flex-shrink-0"></i>
              <div>
                <h4 class="title"><a href="#" class="stretched-link">Magni Dolores</a></h4>
                <p class="description">Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>
              </div>
            </div>
          </div><!-- End Service Item -->

          <div class="col-md-6" data-aos="fade-up" data-aos-delay="500">
            <div class="service-item d-flex position-relative h-100">
              <i class="bi bi-brightness-high icon flex-shrink-0"></i>
              <div>
                <h4 class="title"><a href="#" class="stretched-link">Nemo Enim</a></h4>
                <p class="description">At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque</p>
              </div>
            </div>
          </div><!-- End Service Item -->

          <div class="col-md-6" data-aos="fade-up" data-aos-delay="600">
            <div class="service-item d-flex position-relative h-100">
              <i class="bi bi-calendar4-week icon flex-shrink-0"></i>
              <div>
                <h4 class="title"><a href="#" class="stretched-link">Eiusmod Tempor</a></h4>
                <p class="description">Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi</p>
              </div>
            </div>
          </div><!-- End Service Item -->

        </div>

      </div>

    </section><!-- /Services 2 Section -->

    <!-- FAQ Section -->
    <section id="faq" class="section py-5 light-background">
      <div class="container" data-aos="fade-up">
        <div class="row justify-content-center mb-4">
          <div class="col-lg-8 text-center">
            <h2 class="h3 mb-2">Questions fréquentes</h2>
            <p class="text-muted">Tout ce que vous devez savoir pour préparer votre séjour.</p>
          </div>
        </div>
        <div class="row g-4">
          <div class="col-lg-7">
            <div class="accordion accordion-flush modern-accordion" id="faqAccordion">
              <div class="accordion-item">
                <h2 class="accordion-header" id="q1"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#a1">Comment réserver une activité ?</button></h2>
                <div id="a1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                  <div class="accordion-body">Recherchez une activité, choisissez une date, puis validez votre réservation en ligne en quelques clics.</div>
                </div>
              </div>
              <div class="accordion-item">
                <h2 class="accordion-header" id="q2"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#a2">Puis-je annuler gratuitement ?</button></h2>
                <div id="a2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                  <div class="accordion-body">Selon l’activité et l’hébergement choisis, une annulation gratuite est possible jusqu’à 48h avant.</div>
                </div>
              </div>
              <div class="accordion-item">
                <h2 class="accordion-header" id="q3"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#a3">Les paiements sont-ils sécurisés ?</button></h2>
                <div id="a3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                  <div class="accordion-body">Oui, les paiements sont traités via des prestataires certifiés et chiffrés.</div>
                </div>
              </div>
              <div class="accordion-item">
                <h2 class="accordion-header" id="q4"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#a4">Proposez-vous des guides locaux ?</button></h2>
                <div id="a4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                  <div class="accordion-body">Nous collaborons avec des guides certifiés et expérimentés à travers tout le pays.</div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-5">
            <div class="faq-side card border-0 shadow-sm h-100">
              <img src="{{ asset('assets/img/working-2.jpg') }}" class="card-img-top" alt="Burkina Faso">
              <div class="card-body">
                <h3 class="h5">Besoin d’aide ?</h3>
                <p class="text-muted mb-3">Notre équipe vous accompagne pour organiser un voyage parfait.</p>
                <a href="{{ url('/#contact') }}" class="btn btn-primary">Contacter l’équipe</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Blog / News Section -->
    <section id="blog" class="section py-5">
      <div class="container" data-aos="fade-up">
        <div class="d-flex align-items-end justify-content-between mb-4">
          <div>
            <h2 class="h3 mb-1">À la une</h2>
            <p class="text-muted mb-0">Conseils, itinéraires et inspirations de voyage.</p>
          </div>
          <a href="#" class="btn btn-outline-primary btn-sm">Voir le blog</a>
        </div>
        <div class="row g-4">
          <div class="col-md-6 col-lg-4">
            <div class="card h-100 blog-card">
              <img src="{{ asset('assets/img/about.jpg') }}" class="card-img-top" alt="">
              <div class="card-body">
                <span class="badge bg-light text-dark mb-2">Itinéraire</span>
                <h3 class="h6">48h à Ouagadougou: que faire ?</h3>
                <p class="text-muted small mb-3">Musées, marchés, gastronomie et vie nocturne.</p>
                <a href="#" class="stretched-link">Lire</a>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-lg-4">
            <div class="card h-100 blog-card">
              <img src="{{ asset('assets/img/about-2.jpg') }}" class="card-img-top" alt="">
              <div class="card-body">
                <span class="badge bg-light text-dark mb-2">Conseils</span>
                <h3 class="h6">Préparer sa première randonnée</h3>
                <p class="text-muted small mb-3">Matériel, sécurité et meilleures saisons.</p>
                <a href="#" class="stretched-link">Lire</a>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-lg-4">
            <div class="card h-100 blog-card">
              <img src="{{ asset('assets/img/services.jpg') }}" class="card-img-top" alt="">
              <div class="card-body">
                <span class="badge bg-light text-dark mb-2">Inspiration</span>
                <h3 class="h6">Top 5 des sites à voir absolument</h3>
                <p class="text-muted small mb-3">Des paysages naturels et une culture unique.</p>
                <a href="#" class="stretched-link">Lire</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Bottom CTA Ribbon -->
    <section class="cta-ribbon py-4">
      <div class="container">
        <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">
          <div class="text-white">
            <strong>Prêt à explorer le Burkina Faso ?</strong>
            <span class="ms-2 text-white-50">Créez votre compte et commencez à planifier.</span>
          </div>
          <div class="d-flex gap-2">
            <a class="btn btn-light" href="{{ route('register') }}">Créer un compte</a>
            <a class="btn btn-outline-light" href="{{ route('login') }}">Se connecter</a>
          </div>
        </div>
      </div>
    </section>

    @push('styles')
    <style>
      .modern-accordion .accordion-item{ border:1px solid rgba(0,0,0,.06); border-radius:14px; overflow:hidden; box-shadow:0 6px 18px rgba(0,0,0,.06); margin-bottom:10px; }
      .modern-accordion .accordion-button{ padding:14px 18px; font-weight:600; }
      .modern-accordion .accordion-button:not(.collapsed){ background:#fff7f3; color:#d35400; }
      .blog-card{ border:1px solid rgba(0,0,0,.06); border-radius:16px; overflow:hidden; box-shadow:0 8px 24px rgba(0,0,0,.06); }
      .blog-card img{ height:190px; object-fit:cover; }
      .cta-ribbon{ background: linear-gradient(90deg, #ff7e5f 0%, #feb47b 100%); }
      /* Subtle improvement for testimonials bullets spacing */
      .testimonials .swiper-pagination-bullet{ width:9px; height:9px; }

      /* Hero typography to match mockup scale */
      #hero h1{ letter-spacing:.8px; text-shadow:0 8px 26px rgba(0,0,0,.28); }
      @media (min-width:1200px){ #hero h1{ font-size:3.4rem; } }
      @media (min-width:1400px){ #hero h1{ font-size:3.8rem; } }
      #hero .lead{ max-width:780px; margin-inline:auto; }

      /* Search bar refinements */
      .hero-search .input-wrap i{ color:#9aa3ad; }

      /* Testimonials as white cards over light background */
      .testimonials{ background:#fff !important; }
      .testimonials .testimonials-bg{ display:none !important; }
      .testimonials .testimonial-item{ background:#fff; color:#212529; border:1px solid rgba(0,0,0,.06); border-radius:18px; padding:22px; box-shadow:0 12px 36px rgba(0,0,0,.07); }
      .testimonials .testimonial-img{ width:56px; height:56px; object-fit:cover; border-radius:50%; border:3px solid #fff; box-shadow:0 6px 20px rgba(0,0,0,.08); }
      .testimonials .stars i{ color:#ffa534; }
      .testimonials .quote-icon-left, .testimonials .quote-icon-right{ color:#ff7e5f; opacity:.7; }
      .testimonials .swiper-pagination-bullet-active{ background:#ff7e5f; }
    </style>
    @endpush

    <!-- Testimonials Section -->
    <section id="testimonials" class="testimonials section dark-background">

      <img src="{{ asset('assets/img/testimonials-bg.jpg') }}" class="testimonials-bg" alt="">

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="swiper init-swiper">
          <script type="application/json" class="swiper-config">
            {
              "loop": true,
              "speed": 600,
              "autoplay": {
                "delay": 5000
              },
              "slidesPerView": "auto",
              "pagination": {
                "el": ".swiper-pagination",
                "type": "bullets",
                "clickable": true
              }
            }
          </script>
          <div class="swiper-wrapper">

            <div class="swiper-slide">
              <div class="testimonial-item">
                <img src="{{ asset('assets/img/testimonials/testimonials-1.jpg') }}" class="testimonial-img" alt="">
                <h3>Saul Goodman</h3>
                <h4>Ceo &amp; Founder</h4>
                <div class="stars">
                  <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                </div>
                <p>
                  <i class="bi bi-quote quote-icon-left"></i>
                  <span>Proin iaculis purus consequat sem cure digni ssim donec porttitora entum suscipit rhoncus. Accusantium quam, ultricies eget id, aliquam eget nibh et. Maecen aliquam, risus at semper.</span>
                  <i class="bi bi-quote quote-icon-right"></i>
                </p>
              </div>
            </div><!-- End testimonial item -->

            <div class="swiper-slide">
              <div class="testimonial-item">
                <img src="{{ asset('assets/img/testimonials/testimonials-2.jpg') }}" class="testimonial-img" alt="">
                <h3>Sara Wilsson</h3>
                <h4>Designer</h4>
                <div class="stars">
                  <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                </div>
                <p>
                  <i class="bi bi-quote quote-icon-left"></i>
                  <span>Export tempor illum tamen malis malis eram quae irure esse labore quem cillum quid cillum eram malis quorum velit fore eram velit sunt aliqua noster fugiat irure amet legam anim culpa.</span>
                  <i class="bi bi-quote quote-icon-right"></i>
                </p>
              </div>
            </div><!-- End testimonial item -->

            <div class="swiper-slide">
              <div class="testimonial-item">
                <img src="{{ asset('assets/img/testimonials/testimonials-3.jpg') }}" class="testimonial-img" alt="">
                <h3>Jena Karlis</h3>
                <h4>Store Owner</h4>
                <div class="stars">
                  <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                </div>
                <p>
                  <i class="bi bi-quote quote-icon-left"></i>
                  <span>Enim nisi quem export duis labore cillum quae magna enim sint quorum nulla quem veniam duis minim tempor labore quem eram duis noster aute amet eram fore quis sint minim.</span>
                  <i class="bi bi-quote quote-icon-right"></i>
                </p>
              </div>
            </div><!-- End testimonial item -->

            <div class="swiper-slide">
              <div class="testimonial-item">
                <img src="{{ asset('assets/img/testimonials/testimonials-4.jpg') }}" class="testimonial-img" alt="">
                <h3>Matt Brandon</h3>
                <h4>Freelancer</h4>
                <div class="stars">
                  <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                </div>
                <p>
                  <i class="bi bi-quote quote-icon-left"></i>
                  <span>Fugiat enim eram quae cillum dolore dolor amet nulla culpa multos export minim fugiat minim velit minim dolor enim duis veniam ipsum anim magna sunt elit fore quem dolore labore illum veniam.</span>
                  <i class="bi bi-quote quote-icon-right"></i>
                </p>
              </div>
            </div><!-- End testimonial item -->

            <div class="swiper-slide">
              <div class="testimonial-item">
                <img src="{{ asset('assets/img/testimonials/testimonials-5.jpg') }}" class="testimonial-img" alt="">
                <h3>John Larson</h3>
                <h4>Entrepreneur</h4>
                <div class="stars">
                  <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                </div>
                <p>
                  <i class="bi bi-quote quote-icon-left"></i>
                  <span>Quis quorum aliqua sint quem legam fore sunt eram irure aliqua veniam tempor noster veniam enim culpa labore duis sunt culpa nulla illum cillum fugiat legam esse veniam culpa fore nisi cillum quid.</span>
                  <i class="bi bi-quote quote-icon-right"></i>
                </p>
              </div>
            </div><!-- End testimonial item -->

          </div>
          <div class="swiper-pagination"></div>
        </div>

      </div>

    </section><!-- /Testimonials Section -->

    <!-- Portfolio Section -->
    <section id="portfolio" class="portfolio section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Portfolio</h2>
        <p>CHECK OUR PORTFOLIO</p>
      </div><!-- End Section Title -->

      <div class="container">

        <div class="isotope-layout" data-default-filter="*" data-layout="masonry" data-sort="original-order">

          <ul class="portfolio-filters isotope-filters" data-aos="fade-up" data-aos-delay="100">
            <li data-filter="*" class="filter-active">All</li>
            <li data-filter=".filter-app">App</li>
            <li data-filter=".filter-product">Product</li>
            <li data-filter=".filter-branding">Branding</li>
            <li data-filter=".filter-books">Books</li>
          </ul><!-- End Portfolio Filters -->

          <div class="row gy-4 isotope-container" data-aos="fade-up" data-aos-delay="200">

            <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-app">
              <div class="portfolio-content h-100">
                <img src="{{ asset('assets/img/portfolio/app-1.jpg') }}" class="img-fluid" alt="">
                <div class="portfolio-info">
                  <h4>App 1</h4>
                  <p>Lorem ipsum, dolor sit amet consectetur</p>
                  <a href="{{ asset('assets/img/portfolio/app-1.jpg') }}" title="App 1" data-gallery="portfolio-gallery-app" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
                  <a href="portfolio-details.html" title="More Details" class="details-link"><i class="bi bi-link-45deg"></i></a>
                </div>
              </div>
            </div><!-- End Portfolio Item -->

            <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-product">
              <div class="portfolio-content h-100">
                <img src="{{ asset('assets/img/portfolio/product-1.jpg') }}" class="img-fluid" alt="">
                <div class="portfolio-info">
                  <h4>Product 1</h4>
                  <p>Lorem ipsum, dolor sit amet consectetur</p>
                  <a href="{{ asset('assets/img/portfolio/product-1.jpg') }}" title="Product 1" data-gallery="portfolio-gallery-product" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
                  <a href="portfolio-details.html" title="More Details" class="details-link"><i class="bi bi-link-45deg"></i></a>
                </div>
              </div>
            </div><!-- End Portfolio Item -->

            <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-branding">
              <div class="portfolio-content h-100">
                <img src="{{ asset('assets/img/portfolio/branding-1.jpg') }}" class="img-fluid" alt="">
                <div class="portfolio-info">
                  <h4>Branding 1</h4>
                  <p>Lorem ipsum, dolor sit amet consectetur</p>
                  <a href="{{ asset('assets/img/portfolio/branding-1.jpg') }}" title="Branding 1" data-gallery="portfolio-gallery-branding" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
                  <a href="portfolio-details.html" title="More Details" class="details-link"><i class="bi bi-link-45deg"></i></a>
                </div>
              </div>
            </div><!-- End Portfolio Item -->

            <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-books">
              <div class="portfolio-content h-100">
                <img src="{{ asset('assets/img/portfolio/books-1.jpg') }}" class="img-fluid" alt="">
                <div class="portfolio-info">
                  <h4>Books 1</h4>
                  <p>Lorem ipsum, dolor sit amet consectetur</p>
                  <a href="{{ asset('assets/img/portfolio/books-1.jpg') }}" title="Branding 1" data-gallery="portfolio-gallery-book" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
                  <a href="portfolio-details.html" title="More Details" class="details-link"><i class="bi bi-link-45deg"></i></a>
                </div>
              </div>
            </div><!-- End Portfolio Item -->

            <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-app">
              <div class="portfolio-content h-100">
                <img src="{{ asset('assets/img/portfolio/app-2.jpg') }}" class="img-fluid" alt="">
                <div class="portfolio-info">
                  <h4>App 2</h4>
                  <p>Lorem ipsum, dolor sit amet consectetur</p>
                  <a href="{{ asset('assets/img/portfolio/app-2.jpg') }}" title="App 2" data-gallery="portfolio-gallery-app" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
                  <a href="portfolio-details.html" title="More Details" class="details-link"><i class="bi bi-link-45deg"></i></a>
                </div>
              </div>
            </div><!-- End Portfolio Item -->

            <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-product">
              <div class="portfolio-content h-100">
                <img src="{{ asset('assets/img/portfolio/product-2.jpg') }}" class="img-fluid" alt="">
                <div class="portfolio-info">
                  <h4>Product 2</h4>
                  <p>Lorem ipsum, dolor sit amet consectetur</p>
                  <a href="{{ asset('assets/img/portfolio/product-2.jpg') }}" title="Product 2" data-gallery="portfolio-gallery-product" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
                  <a href="portfolio-details.html" title="More Details" class="details-link"><i class="bi bi-link-45deg"></i></a>
                </div>
              </div>
            </div><!-- End Portfolio Item -->

            <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-branding">
              <div class="portfolio-content h-100">
                <img src="{{ asset('assets/img/portfolio/branding-2.jpg') }}" class="img-fluid" alt="">
                <div class="portfolio-info">
                  <h4>Branding 2</h4>
                  <p>Lorem ipsum, dolor sit amet consectetur</p>
                  <a href="{{ asset('assets/img/portfolio/branding-2.jpg') }}" title="Branding 2" data-gallery="portfolio-gallery-branding" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
                  <a href="portfolio-details.html" title="More Details" class="details-link"><i class="bi bi-link-45deg"></i></a>
                </div>
              </div>
            </div><!-- End Portfolio Item -->

            <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-books">
              <div class="portfolio-content h-100">
                <img src="{{ asset('assets/img/portfolio/books-2.jpg') }}" class="img-fluid" alt="">
                <div class="portfolio-info">
                  <h4>Books 2</h4>
                  <p>Lorem ipsum, dolor sit amet consectetur</p>
                  <a href="{{ asset('assets/img/portfolio/books-2.jpg') }}" title="Branding 2" data-gallery="portfolio-gallery-book" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
                  <a href="portfolio-details.html" title="More Details" class="details-link"><i class="bi bi-link-45deg"></i></a>
                </div>
              </div>
            </div><!-- End Portfolio Item -->

            <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-app">
              <div class="portfolio-content h-100">
                <img src="{{ asset('assets/img/portfolio/app-3.jpg') }}" class="img-fluid" alt="">
                <div class="portfolio-info">
                  <h4>App 3</h4>
                  <p>Lorem ipsum, dolor sit amet consectetur</p>
                  <a href="{{ asset('assets/img/portfolio/app-3.jpg') }}" title="App 3" data-gallery="portfolio-gallery-app" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
                  <a href="portfolio-details.html" title="More Details" class="details-link"><i class="bi bi-link-45deg"></i></a>
                </div>
              </div>
            </div><!-- End Portfolio Item -->

            <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-product">
              <div class="portfolio-content h-100">
                <img src="{{ asset('assets/img/portfolio/product-3.jpg') }}" class="img-fluid" alt="">
                <div class="portfolio-info">
                  <h4>Product 3</h4>
                  <p>Lorem ipsum, dolor sit amet consectetur</p>
                  <a href="{{ asset('assets/img/portfolio/product-3.jpg') }}" title="Product 3" data-gallery="portfolio-gallery-product" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
                  <a href="portfolio-details.html" title="More Details" class="details-link"><i class="bi bi-link-45deg"></i></a>
                </div>
              </div>
            </div><!-- End Portfolio Item -->

            <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-branding">
              <div class="portfolio-content h-100">
                <img src="{{ asset('assets/img/portfolio/branding-3.jpg') }}" class="img-fluid" alt="">
                <div class="portfolio-info">
                  <h4>Branding 3</h4>
                  <p>Lorem ipsum, dolor sit amet consectetur</p>
                  <a href="{{ asset('assets/img/portfolio/branding-3.jpg') }}" title="Branding 2" data-gallery="portfolio-gallery-branding" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
                  <a href="portfolio-details.html" title="More Details" class="details-link"><i class="bi bi-link-45deg"></i></a>
                </div>
              </div>
            </div><!-- End Portfolio Item -->

            <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-books">
              <div class="portfolio-content h-100">
                <img src="{{ asset('assets/img/portfolio/books-3.jpg') }}" class="img-fluid" alt="">
                <div class="portfolio-info">
                  <h4>Books 3</h4>
                  <p>Lorem ipsum, dolor sit amet consectetur</p>
                  <a href="{{ asset('assets/img/portfolio/books-3.jpg') }}" title="Branding 3" data-gallery="portfolio-gallery-book" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
                  <a href="portfolio-details.html" title="More Details" class="details-link"><i class="bi bi-link-45deg"></i></a>
                </div>
              </div>
            </div><!-- End Portfolio Item -->

          </div><!-- End Portfolio Container -->

        </div>

      </div>

    </section><!-- /Portfolio Section -->

    <!-- Team Section -->
    <section id="team" class="team section light-background">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Team</h2>
        <p>CHECK OUR TEAM</p>
      </div><!-- End Section Title -->

      <div class="container">

        <div class="row gy-5">

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
            <div class="member">
              <div class="pic"><img src="{{ asset('assets/img/team/team-1.jpg') }}" class="img-fluid" alt=""></div>
              <div class="member-info">
                <h4>Walter White</h4>
                <span>Chief Executive Officer</span>
                <div class="social">
                  <a href=""><i class="bi bi-twitter-x"></i></a>
                  <a href=""><i class="bi bi-facebook"></i></a>
                  <a href=""><i class="bi bi-instagram"></i></a>
                  <a href=""><i class="bi bi-linkedin"></i></a>
                </div>
              </div>
            </div>
          </div><!-- End Team Member -->

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
            <div class="member">
              <div class="pic"><img src="{{ asset('assets/img/team/team-2.jpg') }}" class="img-fluid" alt=""></div>
              <div class="member-info">
                <h4>Sarah Jhonson</h4>
                <span>Product Manager</span>
                <div class="social">
                  <a href=""><i class="bi bi-twitter-x"></i></a>
                  <a href=""><i class="bi bi-facebook"></i></a>
                  <a href=""><i class="bi bi-instagram"></i></a>
                  <a href=""><i class="bi bi-linkedin"></i></a>
                </div>
              </div>
            </div>
          </div><!-- End Team Member -->

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
            <div class="member">
              <div class="pic"><img src="{{ asset('assets/img/team/team-3.jpg') }}" class="img-fluid" alt=""></div>
              <div class="member-info">
                <h4>William Anderson</h4>
                <span>CTO</span>
                <div class="social">
                  <a href=""><i class="bi bi-twitter-x"></i></a>
                  <a href=""><i class="bi bi-facebook"></i></a>
                  <a href=""><i class="bi bi-instagram"></i></a>
                  <a href=""><i class="bi bi-linkedin"></i></a>
                </div>
              </div>
            </div>
          </div><!-- End Team Member -->

        </div>

      </div>

    </section><!-- /Team Section -->

    <!-- Contact Section -->
    <section id="contact" class="contact section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Contact</h2>
        <p>Necessitatibus eius consequatur</p>
      </div><!-- End Section Title -->

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row gy-4">
          <div class="col-lg-6 ">
            <div class="row gy-4">

              <div class="col-lg-12">
                <div class="info-item d-flex flex-column justify-content-center align-items-center" data-aos="fade-up" data-aos-delay="200">
                  <i class="bi bi-geo-alt"></i>
                  <h3>Address</h3>
                  <p>A108 Adam Street, New York, NY 535022</p>
                </div>
              </div><!-- End Info Item -->

              <div class="col-md-6">
                <div class="info-item d-flex flex-column justify-content-center align-items-center" data-aos="fade-up" data-aos-delay="300">
                  <i class="bi bi-telephone"></i>
                  <h3>Call Us</h3>
                  <p>+1 5589 55488 55</p>
                </div>
              </div><!-- End Info Item -->

              <div class="col-md-6">
                <div class="info-item d-flex flex-column justify-content-center align-items-center" data-aos="fade-up" data-aos-delay="400">
                  <i class="bi bi-envelope"></i>
                  <h3>Email Us</h3>
                  <p>info@example.com</p>
                </div>
              </div><!-- End Info Item -->

            </div>
          </div>

          <div class="col-lg-6">
            <form action="forms/contact.php" method="post" class="php-email-form" data-aos="fade-up" data-aos-delay="500">
              <div class="row gy-4">

                <div class="col-md-6">
                  <input type="text" name="name" class="form-control" placeholder="Your Name" required="">
                </div>

                <div class="col-md-6 ">
                  <input type="email" class="form-control" name="email" placeholder="Your Email" required="">
                </div>

                <div class="col-md-12">
                  <input type="text" class="form-control" name="subject" placeholder="Subject" required="">
                </div>

                <div class="col-md-12">
                  <textarea class="form-control" name="message" rows="4" placeholder="Message" required=""></textarea>
                </div>

                <div class="col-md-12 text-center">
                  <div class="loading">Loading</div>
                  <div class="error-message"></div>
                  <div class="sent-message">Your message has been sent. Thank you!</div>

                  <button type="submit">Send Message</button>
                </div>

              </div>
            </form>
          </div><!-- End Contact Form -->

        </div>

      </div>

    </section><!-- /Contact Section -->

@endsection