document.getElementById('download').addEventListener('click', function() {
    const {
        jsPDF
    } = window.jspdf; // Acceso a jsPDF
    const pdf = new jsPDF(); // Crear instancia del PDF

    // Convertir el contenido HTML en canvas
    html2canvas(document.getElementById('invoice')).then((canvas) => {
        const imgData = canvas.toDataURL('image/png'); // Extraer imagen del canvas
        const imgWidth = 190; // Ajustar ancho
        const imgHeight = (canvas.height * imgWidth) / canvas.width; // Calcular altura proporcional

        pdf.addImage(imgData, 'PNG', 10, 10, imgWidth, imgHeight); // Añadir imagen al PDF
        pdf.save('Factura_SN8478042099.pdf'); // Descargar PDF
    });
});