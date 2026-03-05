document.addEventListener('DOMContentLoaded', function() {
    console.log("--- SYSTEM START: PHP-READY VERSION ---");

    /* ========================================================================
       РАЗДЕЛ 0: ГЛОБАЛЬНЫЕ ХЕЛПЕРЫ
       ======================================================================== */

    const getModal = (id) => {
        const el = document.getElementById(id);
        if (el) {
            return bootstrap.Modal.getOrCreateInstance(el);
        }
        return null;
    };

    function setCookie(name, value, days) {
        let expires = "";
        if (days) {
            const date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + encodeURIComponent(value) + expires + "; path=/";
    }

    function getCookie(name) {
        const nameEQ = name + "=";
        const ca = document.cookie.split(';');
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) === ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) === 0) return decodeURIComponent(c.substring(nameEQ.length, c.length));
        }
        return null;
    }

    function eraseCookie(name) {
        document.cookie = name + '=; Max-Age=-99999999; path=/;';
    }


    /* ========================================================================
       РАЗДЕЛ 1: ВИЗУАЛЬНЫЕ ЭФФЕКТЫ И UI
       ======================================================================== */

    // 1.1. Слайдер Hero
    const slides = document.querySelectorAll('.slide');
    const dotsContainer = document.querySelector('.dots');
    
    if (slides.length > 0 && dotsContainer) {
        let currentSlide = 0;
        dotsContainer.innerHTML = '';
        slides.forEach((_, index) => {
            const dot = document.createElement('button');
            if (index === 0) dot.classList.add('active');
            dot.addEventListener('click', () => showSlide(index));
            dotsContainer.appendChild(dot);
        });
        const dots = dotsContainer.querySelectorAll('button');
        function showSlide(index) {
            slides[currentSlide].classList.remove('active');
            dots[currentSlide].classList.remove('active');
            slides[index].classList.add('active');
            dots[index].classList.add('active');
            currentSlide = index;
        }
        setInterval(() => {
            let nextSlide = (currentSlide + 1) % slides.length;
            showSlide(nextSlide);
        }, 5000);
    }

    // 1.2. Таймер Промо
    let fakeTime = { days: 4, hours: 9, mins: 34, secs: 32 };
    const timerInterval = setInterval(() => {
        fakeTime.secs--;
        if (fakeTime.secs < 0) { fakeTime.secs = 59; fakeTime.mins--; }
        if (fakeTime.mins < 0) { fakeTime.mins = 59; fakeTime.hours--; }
        if (fakeTime.hours < 0) { fakeTime.hours = 23; fakeTime.days--; }
        const elSecs = document.querySelector('.fake-secs');
        if (elSecs) {
            elSecs.textContent = String(fakeTime.secs).padStart(2, '0');
            document.querySelector('.fake-mins').textContent = String(fakeTime.mins).padStart(2, '0');
            document.querySelector('.fake-hours').textContent = String(fakeTime.hours).padStart(2, '0');
            document.querySelector('.fake-days').textContent = String(fakeTime.days).padStart(2, '0');
        } else { clearInterval(timerInterval); }
    }, 1000);

    // 1.3. Калькулятор рассрочки (jQuery)
    if (typeof $ !== 'undefined' && $('#budget').length) {
        function calculatePayment() {
            const budget = parseFloat($('#budget').val()) || 0;
            const months = parseInt($('#months').val()) || 1;
            const downStr = $('#downPayment').val() || "0";
            const downPercent = parseFloat(downStr.replace('%', '')) || 0;
            const downPayment = budget * (downPercent / 100);
            const monthlyPayment = (budget - downPayment) / months;
            $('#paymentResult').text(monthlyPayment.toFixed(2) + ' €');
        }
        $('#budget, #months, #downPayment').on('input change', calculatePayment);
        calculatePayment(); 
    }

    // 1.4. Маска телефона
    const phoneInput = document.getElementById('phoneInput');
    if (phoneInput && typeof IMask !== 'undefined') {
        IMask(phoneInput, { mask: '+375 (00) 000-00-00' });
    }

    // 1.5. Scroll Reveal
    function reveal() {
        const reveals = document.querySelectorAll(".reveal");
        for (let i = 0; i < reveals.length; i++) {
            const windowHeight = window.innerHeight;
            const elementTop = reveals[i].getBoundingClientRect().top;
            if (elementTop < windowHeight - 100) {
                reveals[i].classList.add("active");
            }
        }
    }
    window.addEventListener("scroll", reveal);
    reveal();


    /* ========================================================================
       РАЗДЕЛ 2: КАТАЛОГ И JSON (ЛР 19)
       ======================================================================== */

    let allProducts = [];
    let displayedProducts = [];
    let cart = [];
    let currentFilter = 'all';
    let currentSort = 'default';

    const grid = document.getElementById('portfolioGrid');
    const loadBtn = document.getElementById('loadMoreBtn');
    let shownCount = 0;
    const step = 6;

    function initProducts() {
        fetch('products.json')
            .then(res => res.json())
            .then(data => { 
                allProducts = data; 
                applyFilters(); 
            })
            .catch(() => { 
                console.warn("JSON error. Catalog using static fallback."); 
            });
    }

    function applyFilters() {
        displayedProducts = (currentFilter === 'all') ? [...allProducts] : allProducts.filter(p => p.category === currentFilter);
        if (currentSort === 'price_asc') displayedProducts.sort((a, b) => a.price - b.price);
        else if (currentSort === 'price_desc') displayedProducts.sort((a, b) => b.price - a.price);
        
        if (grid) grid.innerHTML = '';
        shownCount = 0;
        renderProducts();
    }

    function renderProducts() {
        if (!grid) return;
        const nextItems = displayedProducts.slice(shownCount, shownCount + step);
        nextItems.forEach(item => {
            const div = document.createElement('div');
            div.className = 'col-md-4 col-sm-6 reveal active'; 
            div.innerHTML = `
                <div class="portfolio-item">
                    <img class="portfolio-img" src="${item.image}" alt="${item.name}">
                    <div class="portfolio-content">
                        <div class="portfolio-model-name">${item.name}</div>
                        <div class="portfolio-price"><span class="amount">${item.price}</span> LEI</div>
                        <button class="btn btn-warning w-100 mt-2 add-to-cart-action" data-id="${item.id}">В КОРЗИНУ</button>
                    </div>
                </div>`;
            grid.appendChild(div);
        });
        shownCount += nextItems.length;
        if (loadBtn) loadBtn.style.display = (shownCount >= displayedProducts.length) ? 'none' : 'block';
    }

    if(loadBtn) loadBtn.addEventListener('click', renderProducts);
    
    // Фильтры каталога
    document.getElementById('catalogFilters')?.addEventListener('click', (e) => {
        if (e.target.tagName === 'BUTTON') {
            currentFilter = e.target.dataset.filter;
            applyFilters();
        }
    });

    initProducts();


    /* ========================================================================
       РАЗДЕЛ 3: КОРЗИНА
       ======================================================================== */
    
    function addToCart(id) {
        const product = allProducts.find(p => p.id === id);
        if(product) {
            cart.push(product);
            updateCartUI();
        }
    }

    function updateCartUI() {
        const countEl = document.getElementById('cartCount');
        if (countEl) countEl.textContent = cart.length;
        const tbody = document.getElementById('cartItemsContainer');
        const totalEl = document.getElementById('cartTotal');
        if (tbody && totalEl) {
            tbody.innerHTML = cart.length === 0 ? '<tr><td colspan="3" class="text-center">Пусто</td></tr>' : '';
            let total = 0;
            cart.forEach((item, i) => {
                total += item.price;
                tbody.innerHTML += `<tr><td>${item.name}</td><td>${item.price}</td><td><button class="btn btn-sm btn-danger" onclick="window.delCartItem(${i})">&times;</button></td></tr>`;
            });
            totalEl.textContent = total;
        }
    }

    window.delCartItem = (i) => { cart.splice(i, 1); updateCartUI(); };
    
    document.getElementById('cartBtn')?.addEventListener('click', () => getModal('cartModal').show());
    grid?.addEventListener('click', (e) => {
        const btn = e.target.closest('.add-to-cart-action');
        if (btn) addToCart(parseInt(btn.dataset.id));
    });


    /* ========================================================================
       РАЗДЕЛ 5: ДАННЫЕ ПРОФИЛЯ (ЛР 20)
       ======================================================================== */
    
    function getPersonalData() {
        return {
            fio: document.getElementById('pdFIO')?.value || '',
            email: document.getElementById('pdEmail')?.value || '',
            phone: document.getElementById('pdPhone')?.value || '',
            dob: document.getElementById('pdDob')?.value || '',
            place: document.getElementById('pdPlace')?.value || ''
        };
    }

    function setPersonalData(d) {
        if(!d) return;
        if(document.getElementById('pdFIO')) document.getElementById('pdFIO').value = d.fio || '';
        if(document.getElementById('pdEmail')) document.getElementById('pdEmail').value = d.email || '';
        if(document.getElementById('pdPhone')) document.getElementById('pdPhone').value = d.phone || '';
        if(document.getElementById('pdDob')) document.getElementById('pdDob').value = d.dob || '';
        if(document.getElementById('pdPlace')) document.getElementById('pdPlace').value = d.place || '';
    }

    document.getElementById('btnSaveLS')?.addEventListener('click', () => {
        localStorage.setItem('userData', JSON.stringify(getPersonalData()));
        alert("Сохранено в Local Storage!");
    });
    document.getElementById('btnLoadLS')?.addEventListener('click', () => {
        const d = localStorage.getItem('userData');
        if(d) setPersonalData(JSON.parse(d)); else alert("LS пуст");
    });
    document.getElementById('btnSaveCookie')?.addEventListener('click', () => {
        setCookie('userDataFull', JSON.stringify(getPersonalData()), 7);
        alert("Сохранено в Cookie!");
    });
    document.getElementById('btnLoadCookie')?.addEventListener('click', () => {
        const d = getCookie('userDataFull');
        if(d) setPersonalData(JSON.parse(d)); else alert("Cookie пуст");
    });


    /* ========================================================================
       РАЗДЕЛ 6: ОФОРМЛЕНИЕ ЗАКАЗА
       ======================================================================== */
    
    const checkoutBtn = document.getElementById('btnInitCheckout');
    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', function() {
            if (cart.length === 0) { alert("Корзина пуста!"); return; }

            // ПРОВЕРКА АВТОРИЗАЦИИ ЧЕРЕЗ КЛАСС В ШАПКЕ (установлен PHP)
            const profileLink = document.querySelector('.nav-link.text-success.fw-bold');
            
            if (!profileLink) {
                getModal('cartModal').hide();
                alert("Пожалуйста, войдите в аккаунт для оформления заказа.");
                setTimeout(() => getModal('guestModal').show(), 400);
            } else {
                getModal('cartModal').hide();
                const total = document.getElementById('cartTotal').textContent;
                const checkoutTotal = document.getElementById('checkoutTotal');
                if(checkoutTotal) checkoutTotal.textContent = total + " лей";
                setTimeout(() => getModal('checkoutModal').show(), 400);
            }
        });
    }

});