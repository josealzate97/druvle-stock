document.addEventListener("DOMContentLoaded", () => {

    console.log("Categories JS Loaded!");

    const searchInput = document.getElementById('categoriesSearch');
    const statusSelect = document.getElementById('categoriesStatusFilter');
    const table = document.querySelector('.section-table');

    if (searchInput && statusSelect && table) {
        const rows = Array.from(table.querySelectorAll('tbody tr'));

        const filterRows = () => {
            const query = searchInput.value.trim().toLowerCase();
            const status = statusSelect.value;

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                const matchesQuery = !query || text.includes(query);
                const matchesStatus = !status || row.dataset.status === status;
                row.style.display = matchesQuery && matchesStatus ? '' : 'none';
            });
        };

        searchInput.addEventListener('input', filterRows);
        statusSelect.addEventListener('change', filterRows);
    }

    const categoryForm = document.getElementById('categoryForm');
    const modalTitle = document.getElementById('categoryModalLabel');
    const categoryModalElement = document.getElementById('categoryModal');
    const categoryModal = new bootstrap.Modal(categoryModalElement); 

    // Campos que se usaran para la generacion de la abreviatura en la categoria
    const nameInput = document.getElementById('categoryName');
    const abbrInput = document.getElementById('categoryAbbr');

    categoryModalElement.addEventListener('show.bs.modal', function (event) {
        // Detecta el botón que disparó el modal
        const trigger = event.relatedTarget;
        const mode = trigger ? trigger.getAttribute('data-bs-mode') : null;

        if (mode === 'new') {
            clearCategoryModal();
        }
    });

    const categoryIdInput = document.getElementById('categoryId');

    // Evento para manejar el formulario
    categoryForm.addEventListener('submit', async function (event) {

        event.preventDefault();

        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());
        const url = data.id ? `/categories/update/${data.id}` : '/categories/create'; // URL dinámica

        try {

            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify(data)
            });

            if (response.ok) {

                notyf.success(data.id ? 'Categoría actualizada correctamente' : 'Categoría creada correctamente');
                
                // Cierra el modal
                categoryModal.hide();

                setTimeout(() => {
                    location.reload();
                }, 3000);
                
            } else {

                const errorData = await response.json();
                notyf.error(errorData.message || 'Error al guardar la categoría');

            }

        } catch (error) {
            notyf.error('Error de red');
        }

    });

    // Función para generar la abreviatura
    nameInput.addEventListener('input', function() {

        let words = nameInput.value.trim().split(' ').filter(Boolean);
        let abbr = '';

        if (words.length === 1) {
            abbr = words[0].substring(0, 4);
        } else if (words.length === 2) {
            abbr = (words[0].substring(0, 2) + words[1].substring(0, 2));
        } else {
            // Toma la primera letra de las primeras 4 palabras
            abbr = words.slice(0, 4).map(word => word[0]).join('');
        }

        abbrInput.value = abbr;

    });

    // Función para cargar datos en el modal
    window.editCategory = async function (categoryId) {

        try {

            const response = await fetch(`/categories/getCategory/${categoryId}`);
            const data = await response.json();

            if (data.success) {

                modalTitle.textContent = 'Editar Categoría';
                categoryIdInput.value = data.category.id;

                document.getElementById('categoryName').value = data.category.name;
                document.getElementById('categoryAbbr').value = data.category.abbreviation;
                document.getElementById('categoryIcon').value = data.category.icon;
                document.getElementById('categoryColor').value = data.category.color;

                const categoryModal = new bootstrap.Modal(document.getElementById('categoryModal'));
                categoryModal.show();

            } else {

                notyf.error('Error al cargar la categoría');

            }

        } catch (error) {

            notyf.error('Error de red');
        }
    };

});

const notyf = new Notyf();

window.deleteCategory = deleteCategory;
window.activateCategory = activateCategory;

/**
 * función para eliminar una categoría
 * @param {*} categoryId 
 * @return {Promise<void>} - Promesa que se resuelve cuando la categoría es eliminada
*/
async function deleteCategory(categoryId) {

    try {

        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        const response = await fetch(`/categories/delete/${categoryId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token
            }
        });

        if (response.ok) {

            notyf.success('Categoría eliminada correctamente');

            const badge = document.querySelector(`tr[data-id="${categoryId}"] .badge`);

            badge.textContent = 'Inactivo';
            badge.classList.remove('bg-success');
            badge.classList.add('bg-danger');
            
            setTimeout(() => {
                location.reload();
            }, 3000);
        
        } else {

            notyf.error('Error al eliminar la categoría');

        }

    } catch (e) {
        notyf.error('Error de red');
    }
}

async function activateCategory(categoryId) {

    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch(`/categories/activate/${categoryId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token
        }
    })
    .then(response => response.json())
    .then(data => {
        
        if (data.success) {

            notyf.success(data.message);

            setTimeout(() => {
                location.reload();
            }, 3000);

        } else {
            notyf.error(data.message || 'Error al activar la categoría');
        }

    })
    .catch(() => {
        notyf.error('Error de red');
    });
}


/** 
 * Función para limpiar el modal de categoría
 */
function clearCategoryModal() {

    const modalTitle = document.getElementById('categoryModalLabel');
    
    if (modalTitle) {
        modalTitle.textContent = 'Crear Categoría';
    }

    document.getElementById('categoryId').value = '';
    document.getElementById('categoryName').value = '';
    document.getElementById('categoryAbbr').value = '';
    document.getElementById('categoryIcon').value = '';
    document.getElementById('categoryColor').value = '#000000'; // Valor por defecto
}
