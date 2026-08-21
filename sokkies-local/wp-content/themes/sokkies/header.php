<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div class="topbar">
        <ul>
        <li><span>Vanaf <?php echo esc_html( sokkies_optie( 'minimale_afname', '30' ) ); ?> paar</span></li>
        <li>Eigen productie</li>
        <li>Gratis ontwerp binnen 24u</li>
        <li>Gratis verzending</li>
        <li><?php echo esc_html( sokkies_optie( 'review_score', '9.5/10' ) . ' uit ' . sokkies_optie( 'review_aantal', '450+' ) ); ?> reviews</li>
        </ul>
    </div>

<header>
      <div class="container">
        <div class="nav-wrap">
          <nav class="navbar">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="logo">
            <svg id="Group_235" data-name="Group 235" xmlns="http://www.w3.org/2000/svg" width="134.897" height="42" viewBox="0 0 134.897 42">
            <path id="Path_3662" data-name="Path 3662" d="M133.793,42.057h-8.01a.644.644,0,0,1-.643-.642V1.452a.644.644,0,0,1,.643-.642h8.01a.644.644,0,0,1,.643.642V41.416a.644.644,0,0,1-.643.642" transform="translate(-80.041 -0.517)" fill="#fa4b46"/>
            <path id="Path_3663" data-name="Path 3663" d="M1.029,27.122h6.11a1.017,1.017,0,0,1,1.015,1.009c.029,5.663.311,6.615,1.438,6.615.939,0,1.254-.624,1.254-4.311,0-2.5-.249-3.374-.878-4.063A17.714,17.714,0,0,0,6.773,24.31a15.077,15.077,0,0,1-4.26-3.125c-1.377-1.5-2.5-4.376-2.5-8.875C.006,3.749,3.576,0,9.9,0c7.54,0,9.473,3.237,9.582,14.167a1.014,1.014,0,0,1-1.015,1.02H12.735a1.017,1.017,0,0,1-1.015-1.009c-.018-6.193-.231-6.928-1.062-6.928-.939,0-1.315.811-1.315,4.063,0,2.873.249,4.063.939,4.686a15.452,15.452,0,0,0,3.57,2.249,12.765,12.765,0,0,1,4.26,3.313c1.377,1.939,2.005,4.376,2.005,8.186,0,9-2.945,12.249-10.21,12.249C2.152,42,.136,37.85.014,28.146a1.015,1.015,0,0,1,1.015-1.024" transform="translate(-0.01 0)" fill="#fa4b46"/>
            <path id="Path_3664" data-name="Path 3664" d="M72.993,20.81c0-11.063-.376-12.123-1.315-12.123-1,0-1.315,1.063-1.315,12.123,0,11.625.314,12.5,1.315,12.5.939,0,1.315-.876,1.315-12.5m-11.963,0C61.03,5.436,63.91,0,71.677,0S82.325,5.436,82.325,20.81,79.507,42,71.677,42,61.03,37.122,61.03,20.81" transform="translate(-39.043)" fill="#fa4b46"/>
            <path id="Path_3665" data-name="Path 3665" d="M272.415.84h13.13a1.015,1.015,0,0,1,1.015,1.013V8.514a1.015,1.015,0,0,1-1.015,1.013h-3.8a1.015,1.015,0,0,0-1.015,1.013v6.787a1.015,1.015,0,0,0,1.015,1.013h2.731a1.015,1.015,0,0,1,1.015,1.013v3.915a1.015,1.015,0,0,1-1.015,1.013h-2.731a1.015,1.015,0,0,0-1.015,1.013v6.975a1.015,1.015,0,0,0,1.015,1.013h4.173a1.015,1.015,0,0,1,1.015,1.013v6.661a1.015,1.015,0,0,1-1.015,1.013H272.415a1.015,1.015,0,0,1-1.015-1.013V1.853A1.015,1.015,0,0,1,272.415.84" transform="translate(-173.6 -0.537)" fill="#fa4b46"/>
            <path id="Path_3666" data-name="Path 3666" d="M319.6,27.122h6.11a1.017,1.017,0,0,1,1.015,1.009c.029,5.663.311,6.615,1.438,6.615.939,0,1.254-.624,1.254-4.311,0-2.5-.249-3.374-.878-4.063a17.714,17.714,0,0,0-3.194-2.062,15.077,15.077,0,0,1-4.26-3.125c-1.377-1.5-2.5-4.376-2.5-8.875,0-8.561,3.57-12.31,9.9-12.31,7.537,0,9.47,3.237,9.578,14.167a1.012,1.012,0,0,1-1.015,1.02H331.3a1.017,1.017,0,0,1-1.015-1.009c-.018-6.193-.231-6.928-1.062-6.928-.939,0-1.315.811-1.315,4.063,0,2.873.249,4.063.939,4.686a15.452,15.452,0,0,0,3.57,2.249,12.765,12.765,0,0,1,4.26,3.313c1.377,1.939,2.005,4.376,2.005,8.186,0,9-2.945,12.249-10.21,12.249-7.75,0-9.766-4.145-9.892-13.849a1.015,1.015,0,0,1,1.015-1.024" transform="translate(-203.789 0)" fill="#fa4b46"/>
            <path id="Path_3667" data-name="Path 3667" d="M245.453,42.057h-8.01a.644.644,0,0,1-.643-.642V1.452a.644.644,0,0,1,.643-.642h8.01a.644.644,0,0,1,.643.642V41.416a.644.644,0,0,1-.643.642" transform="translate(-151.457 -0.517)" fill="#fa4b46"/>
            <g id="Group_234" data-name="Group 234" transform="translate(51.198 0.296)">
                <path id="Path_3668" data-name="Path 3668" d="M152.906,22.192l6.626,13.521a4.908,4.908,0,0,1-9.126,3.612l-8.126-17.288a1.973,1.973,0,0,1,.022-1.518l8.151-18.5A1.987,1.987,0,0,1,153.062.982l5.365,2.311a1.978,1.978,0,0,1,1.037,2.6l-6.536,14.776a1.973,1.973,0,0,0-.022,1.518" transform="translate(-142.139 -0.82)" fill="#fa4b46"/>
                <path id="Path_3669" data-name="Path 3669" d="M192.866,22.476,199.492,36a4.908,4.908,0,0,1-9.126,3.612l-8.126-17.288a1.973,1.973,0,0,1,.022-1.518L190.413,2.3a1.986,1.986,0,0,1,2.609-1.035l5.365,2.314a1.978,1.978,0,0,1,1.037,2.6l-6.536,14.776a1.973,1.973,0,0,0-.022,1.518" transform="translate(-167.662 -0.999)" fill="#fa4b46"/>
            </g>
            </svg>

            </a>
            <button class="nav-burger" aria-label="Menu" aria-expanded="false">
              <span></span><span></span><span></span>
            </button>
            <div class="navbar-inner">
                <ul class="menu">
                <?php foreach ( sokkies_hoofdmenu() as $item ) :
                    $klassen = 'menu-link';
                    if ( ! empty( $item['alleen_mobiel'] ) ) { $klassen .= ' menu-home'; }
                    if ( ! empty( $item['mega'] ) )          { $klassen .= ' has-mega'; }
                    if ( ! empty( $item['actief'] ) )        { $klassen .= ' active'; }
                ?>
                    <li class="<?php echo esc_attr( $klassen ); ?>"><a href="<?php echo esc_url( $item['url'] ); ?>"<?php echo ! empty( $item['target'] ) ? ' target="' . esc_attr( $item['target'] ) . '"' : ''; ?>><?php echo esc_html( $item['label'] ); ?><?php if ( ! empty( $item['mega'] ) ) : ?> <span class="caret"><svg xmlns="http://www.w3.org/2000/svg" width="11.414" height="6.414" viewBox="0 0 11.414 6.414">
                        <path id="Path_218" data-name="Path 218" d="M482.224,63.112l5,5,5-5" transform="translate(-481.517 -62.405)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/>
                        </svg>
                        </span>
                        <?php endif; ?></a>
                        <?php if ( ! empty( $item['mega'] ) ) { get_template_part( 'template-parts/deel', 'mega' ); } ?>
                    </li>
                <?php endforeach; ?>
                </ul>
                <div class="actions">
                  <div class="search-header-icon">
                    <button class="icon-btn" aria-label="Zoeken">
                    <svg xmlns="http://www.w3.org/2000/svg" width="21.28" height="21.28" viewBox="0 0 21.28 21.28">
                        <g id="Search_1_" transform="translate(-13.612 -19.25)">
                            <circle id="Ellipse_3" data-name="Ellipse 3" cx="8.5" cy="8.5" r="8.5" transform="translate(17.142 20)" fill="none" stroke="#28121b" stroke-miterlimit="10" stroke-width="1.5"/>
                            <line id="Line_84" data-name="Line 84" y1="5.772" x2="5.772" transform="translate(14.142 34.228)" fill="none" stroke="#28121b" stroke-miterlimit="10" stroke-width="1.5"/>
                        </g>
                    </svg>
                  </button>
                    <form class="nav-search" role="search">
                      <span class="nav-search-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="21.28" height="21.28" viewBox="0 0 21.28 21.28">
                          <g transform="translate(-13.612 -19.25)">
                            <circle cx="8.5" cy="8.5" r="8.5" transform="translate(17.142 20)" fill="none" stroke="#28121b" stroke-miterlimit="10" stroke-width="1.5"/>
                            <line y1="5.772" x2="5.772" transform="translate(14.142 34.228)" fill="none" stroke="#28121b" stroke-miterlimit="10" stroke-width="1.5"/>
                          </g>
                        </svg>
                      </span>
                      <input type="search" class="nav-search-input" placeholder="Zoek collecties, downloads, pagina's…" aria-label="Zoeken">
                      <button type="button" class="nav-search-close" aria-label="Sluiten">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="#28121b" stroke-width="1.5" stroke-linecap="round"><path d="M4 4l8 8M12 4l-8 8"/></svg>
                      </button>
                    </form>
                  </div>
                  

                  <button class="icon-btn" aria-label="Account">
                    <svg id="g10334" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="20" height="20" viewBox="0 0 20 20">
                        <defs>
                            <clipPath id="clip-path">
                            <path id="path10338" d="M0-682.665H20v20H0Z" transform="translate(0 682.665)" fill="none" stroke="#28121b" stroke-width="1.5"/>
                            </clipPath>
                        </defs>
                        <g id="g10336" clip-path="url(#clip-path)">
                            <g id="g10342" transform="translate(5 0.781)">
                            <path id="path10344" d="M0-165.666a5,5,0,0,1,5-5,5,5,0,0,1,5,5,5,5,0,0,1-5,5A5,5,0,0,1,0-165.666Z" transform="translate(0 170.666)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-width="1.5"/>
                            </g>
                            <g id="g10346" transform="translate(0.781 10.781)">
                            <path id="path10348" d="M-494.537-37.534a8.616,8.616,0,0,1,2.191,1.369,3.9,3.9,0,0,1,1.321,2.928v1.445a1.563,1.563,0,0,1-1.563,1.563H-507.9a1.563,1.563,0,0,1-1.563-1.562v-1.445a3.9,3.9,0,0,1,1.321-2.928,11.85,11.85,0,0,1,7.9-2.5" transform="translate(509.463 38.667)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-miterlimit="10" stroke-width="1.5"/>
                            </g>
                        </g>
                    </svg>
                  </button>
                  <button class="cta">Gratis proefdesign</button>
                </div>
            </div>

          </nav>
          <div class="lang" data-value="nl">
            <button type="button" class="globe lang-trigger" aria-label="Taal" aria-haspopup="listbox" aria-expanded="false">
              <svg xmlns="http://www.w3.org/2000/svg" width="21.5" height="21.5" viewBox="0 0 21.5 21.5">
                  <g id="globe-1_curved" transform="translate(0.75 0.75)">
                      <path id="Path_3661" data-name="Path 3661" d="M22.01,12.01a10,10,0,0,1-10,10m10-10a10,10,0,0,0-10-10m10,10h-20m10,10a10,10,0,0,1-10-10m10,10a12.885,12.885,0,0,0,4.444-10,12.885,12.885,0,0,0-4.444-10m0,20a12.885,12.885,0,0,1-4.444-10,12.885,12.885,0,0,1,4.444-10m-10,10a10,10,0,0,1,10-10" transform="translate(-2.01 -2.01)" fill="none" stroke="#28121b" stroke-width="1.5"/>
                  </g>
              </svg>
            </button>
            <ul class="lang-list" role="listbox" aria-label="Taal">
              <li class="lang-option" role="option" data-value="nl" data-label="NL"><span class="lang-flag">NL</span></li>
              <li class="lang-option" role="option" data-value="en" data-label="EN"><span class="lang-flag">GB</span> </li>
              <li class="lang-option" role="option" data-value="de" data-label="DE"><span class="lang-flag">DE</span></li>
              <li class="lang-option" role="option" data-value="fr" data-label="FR"><span class="lang-flag">FR</span></li>
            </ul>
          </div>
        </div>
      </div>
    </header>
