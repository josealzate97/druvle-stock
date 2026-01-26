document.addEventListener("DOMContentLoaded", () => {
    console.log("Reports Js Loaded!");
});

function formatCurrency(value) {
    return new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR', minimumFractionDigits: 2 }).format(value);
}

document.addEventListener('alpine:init', () => {

    Alpine.data('reportsApp', () => ({
        activeTab: 'productos',
        filters: {
            productos: { from: '', to: '' },
            ventas: { from: '', to: '' },
            impuestos: { from: '', to: '' }
        },
        formatCurrency(value) {
            return new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR', minimumFractionDigits: 2 }).format(value);
        },
        data: {
            productos: [],
            ventas: [],
            impuestos: []
        },
        loading: false,

        init() {
            this.fetchProductos();
        },

        // Metodo para cargar los datos del reporte productos
        async fetchProductos() {

            this.loading = true;

            await new Promise(resolve => setTimeout(resolve, 1000)); // Espera 1 segundos

            try {

                const params = new URLSearchParams(this.filters.productos).toString();
                const res = await fetch(`/reports/products?${params}`);

                
                if (!res.ok) {
                    throw new Error(`HTTP error! status: ${res.status}`);
                }

                const data = await res.json();
                this.data.productos = data;

            } catch (error) {

                console.error('Error al cargar productos:', error);
                this.data.productos = [];

                notyf.error('No se pudieron cargar los productos');
            }

            this.loading = false;

        },

        // Metodo para cargar los datos del reporte ventas
        async fetchVentas() {

            this.loading = true;

            await new Promise(resolve => setTimeout(resolve, 1000)); // Espera 1 segundos

            try {

                const params = new URLSearchParams(this.filters.ventas).toString();
                const res = await fetch(`/reports/sales?${params}`);

                if (!res.ok) {
                    throw new Error(`HTTP error! status: ${res.status}`);
                }

                const data = await res.json();
                this.data.ventas = data;

            } catch (error) {

                console.error('Error al cargar ventas:', error);
                this.data.ventas = [];
                
                notyf.error('No se pudieron cargar las ventas');
            }
            
            this.loading = false;

        },

        // Metodo para cargar los datos del reporte impuestos
        async fetchImpuestos() {

            this.loading = true;

            await new Promise(resolve => setTimeout(resolve, 1000)); // Espera 1 segundos

            try {

                 const params = new URLSearchParams(this.filters.impuestos).toString();
                 const res = await fetch(`/reports/taxes?${params}`);

                if (!res.ok) {
                    throw new Error(`HTTP error! status: ${res.status}`);
                }

                const data = await res.json();
                this.data.impuestos = data;

            } catch (error) {

                console.error('Error al cargar impuestos:', error);
                this.data.impuestos = [];

                notyf.error('No se pudieron cargar los impuestos');
            }
            
            this.loading = false;

        },

        // Exportacion de productos en excel - pdf
        exportProductos(format) {

            const params = new URLSearchParams(this.filters.productos).toString();
            window.open(`/reports/products/export/${format}?${params}`, '_blank');

        },

        // Exportacion de ventas en excel - pdf
        exportVentas(format) {

            const params = new URLSearchParams(this.filters.ventas).toString();
            window.open(`/reports/sales/export/${format}?${params}`, '_blank');

        },

        // Exportacion de impuestos en excel - pdf
        exportImpuestos(format) {

            const params = new URLSearchParams(this.filters.impuestos).toString();
            window.open(`/reports/taxes/export/${format}?${params}`, '_blank');
            
        },


        // Carga un fetch segun el tab
        setTab(tab) {

            this.activeTab = tab;

            if (tab === 'productos') this.fetchProductos();
            if (tab === 'ventas') this.fetchVentas();
            if (tab === 'impuestos') this.fetchImpuestos();

        },

        formatDate(dateStr) {

            if (!dateStr) return '';

            const date = new Date(dateStr);

            if (isNaN(date)) return dateStr; // Si no es una fecha válida, retorna el valor original
            
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();

            return `${day}/${month}/${year}`;
        }
        
    }));
    
});

const notyf = new Notyf();
