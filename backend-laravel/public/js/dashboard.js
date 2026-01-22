// dashboard.js - Scripts principais do dashboard

document.addEventListener('DOMContentLoaded', function() {
    // CSRF Token para requisições AJAX
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    // Configurar AJAX com token CSRF
    if (csrfToken) {
        fetch.defaults = {
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        };
    }

    // ==================== SIDEBAR ====================
    initSidebar();
    
    // ==================== NOTIFICATIONS ====================
    initNotifications();
    
    // ==================== PROFILE ====================
    initProfile();
    
    // ==================== SEARCH ====================
    initSearch();
    
    // ==================== FILTERS ====================
    initFilters();
});

// ==================== SIDEBAR FUNCTIONS ====================
function initSidebar() {
    const sidebar = document.querySelector('.sidebar');
    const menuLinks = document.querySelectorAll('.menu-link');
    
    // Mobile toggle (você pode adicionar um botão de menu)
    const toggleBtn = document.getElementById('sidebarToggle');
    if (toggleBtn) {
        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });
    }
    
    // Highlight active menu
    menuLinks.forEach(link => {
        if (link.href === window.location.href) {
            link.classList.add('active');
        }
    });
}

// ==================== NOTIFICATIONS ====================
function initNotifications() {
    const notificationBtn = document.getElementById('notificationBtn');
    
    if (notificationBtn) {
        notificationBtn.addEventListener('click', async () => {
            try {
                // Aqui você faria uma requisição para buscar notificações
                // const response = await fetch('/api/notifications');
                // const notifications = await response.json();
                
                // Por enquanto, apenas mostrar um alert
                showNotificationDropdown();
            } catch (error) {
                console.error('Erro ao carregar notificações:', error);
            }
        });
    }
}

function showNotificationDropdown() {
    // Implementar dropdown de notificações
    alert('Você tem novas notificações!');
}

// ==================== PROFILE ====================
function initProfile() {
    const profileBtn = document.getElementById('profileBtn');
    
    if (profileBtn) {
        profileBtn.addEventListener('click', () => {
            // Redirecionar para perfil ou mostrar dropdown
            window.location.href = '/perfil';
        });
    }
}

// ==================== SEARCH ====================
function initSearch() {
    const searchForm = document.querySelector('.search-form');
    const searchInput = document.querySelector('.search-form input[type="text"]');
    const searchBtn = document.querySelector('.btn-search');
    
    if (searchForm) {
        searchForm.addEventListener('submit', (e) => {
            e.preventDefault();
            performSearch();
        });
    }
    
    if (searchBtn) {
        searchBtn.addEventListener('click', (e) => {
            e.preventDefault();
            performSearch();
        });
    }
}

async function performSearch() {
    const searchInput = document.querySelector('.search-form input[type="text"]');
    const subjectSelect = document.querySelector('.search-form select');
    
    const query = searchInput?.value || '';
    const subject = subjectSelect?.value || '';
    
    if (!query && !subject) {
        alert('Digite algo para buscar');
        return;
    }
    
    try {
        // Fazer requisição de busca
        const params = new URLSearchParams({
            q: query,
            materia: subject
        });
        
        // Você pode fazer um fetch aqui ou simplesmente recarregar com os parâmetros
        window.location.href = `/buscar-sala?${params.toString()}`;
        
        // Ou fazer via AJAX:
        // const response = await fetch(`/api/buscar-professores?${params}`);
        // const results = await response.json();
        // renderTeachers(results);
    } catch (error) {
        console.error('Erro na busca:', error);
        alert('Erro ao realizar busca');
    }
}

// ==================== FILTERS ====================
function initFilters() {
    const chips = document.querySelectorAll('.chip');
    const sortSelect = document.querySelector('.sort-select');
    
    // Filter chips
    chips.forEach(chip => {
        chip.addEventListener('click', () => {
            // Toggle active
            chips.forEach(c => c.classList.remove('active'));
            chip.classList.add('active');
            
            // Aplicar filtro
            const filterType = chip.textContent.trim();
            applyFilter(filterType);
        });
    });
    
    // Sort select
    if (sortSelect) {
        sortSelect.addEventListener('change', (e) => {
            const sortBy = e.target.value;
            applySorting(sortBy);
        });
    }
}

function applyFilter(filterType) {
    console.log('Aplicando filtro:', filterType);
    // Implementar lógica de filtro
    // Pode fazer requisição AJAX ou filtrar elementos existentes
}

function applySorting(sortBy) {
    console.log('Ordenando por:', sortBy);
    // Implementar lógica de ordenação
}

// ==================== TEACHER CARDS ====================
function renderTeachers(teachers) {
    const grid = document.querySelector('.teachers-grid');
    if (!grid) return;
    
    grid.innerHTML = '';
    
    teachers.forEach(teacher => {
        const card = createTeacherCard(teacher);
        grid.appendChild(card);
    });
}

function createTeacherCard(teacher) {
    const card = document.createElement('div');
    card.className = 'teacher-card';
    
    if (teacher.isLive) {
        card.innerHTML += `
            <div class="live-badge">
                <i class="fas fa-circle"></i>
                AO VIVO
            </div>
        `;
    }
    
    card.innerHTML += `
        <div class="teacher-header">
            <div class="teacher-avatar" style="background: ${teacher.avatarColor}">
                <i class="fas fa-user-tie"></i>
            </div>
            <div class="teacher-info">
                <h3>${teacher.nome}</h3>
                <p class="teacher-subject">${teacher.materia}</p>
                <div class="teacher-rating">
                    ${generateStars(teacher.rating)}
                    <strong>${teacher.rating}</strong>
                    <span>(${teacher.avaliacoes} avaliações)</span>
                </div>
            </div>
        </div>
        <div class="teacher-details">
            <div class="detail-row">
                <i class="fas fa-graduation-cap"></i>
                <span>${teacher.especialidade}</span>
            </div>
            <div class="detail-row">
                <i class="fas fa-users"></i>
                <span>${teacher.alunos} alunos</span>
            </div>
            <div class="detail-row">
                <i class="fas fa-clock"></i>
                <span>${teacher.horario}</span>
            </div>
        </div>
        <div class="teacher-tags">
            ${teacher.tags.map(tag => `<span class="tag">${tag}</span>`).join('')}
        </div>
        <div class="teacher-footer">
            <button class="btn-primary" onclick="entrarNaSala(${teacher.id})">
                <i class="fas fa-sign-in-alt"></i>
                ${teacher.isLive ? 'Entrar na Sala' : 'Agendar Aula'}
            </button>
            <button class="btn-secondary" onclick="verDetalhes(${teacher.id})">
                <i class="fas fa-info-circle"></i>
            </button>
        </div>
    `;
    
    return card;
}

function generateStars(rating) {
    let stars = '';
    const fullStars = Math.floor(rating);
    const hasHalfStar = rating % 1 !== 0;
    
    for (let i = 0; i < fullStars; i++) {
        stars += '<i class="fas fa-star"></i>';
    }
    
    if (hasHalfStar) {
        stars += '<i class="fas fa-star-half-alt"></i>';
    }
    
    return stars;
}

// ==================== ACTIONS ====================
function entrarNaSala(teacherId) {
    window.location.href = `/sala/${teacherId}`;
}

function verDetalhes(teacherId) {
    window.location.href = `/professor/${teacherId}`;
}

// ==================== UTILITY FUNCTIONS ====================
function showLoading() {
    // Mostrar indicador de carregamento
    const loader = document.createElement('div');
    loader.id = 'global-loader';
    loader.innerHTML = '<div class="spinner"></div>';
    document.body.appendChild(loader);
}

function hideLoading() {
    const loader = document.getElementById('global-loader');
    if (loader) {
        loader.remove();
    }
}

function showToast(message, type = 'info') {
    // Mostrar notificação toast
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.classList.add('show');
    }, 100);
    
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// ==================== EXPORT FUNCTIONS ====================
window.entrarNaSala = entrarNaSala;
window.verDetalhes = verDetalhes;
window.showToast = showToast;