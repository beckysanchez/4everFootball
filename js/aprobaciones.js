document.addEventListener("DOMContentLoaded", () => {
  cargarPublicaciones();

  document.getElementById("filterForm").addEventListener("submit", (e) => {
    e.preventDefault();
    cargarPublicaciones();
  });
});

function cargarPublicaciones() {
  const q = document.getElementById("q").value;
  const cat = document.getElementById("cat").value;
  const sede = document.getElementById("sede").value;
  const estado = document.getElementById("estado").value;
  const orden = document.getElementById("orden").value;

  fetch(`api/publicaciones_listar.php?q=${encodeURIComponent(q)}&cat=${cat}&sede=${sede}&estado=${estado}&orden=${orden}`)
    .then(r => r.json())
    .then(d => {
      const tbody = document.getElementById("tbody");
      tbody.innerHTML = "";
      if (!d.ok) return alert(d.error);

      if (d.data.length === 0) {
        tbody.innerHTML = `<tr><td colspan="8" class="text-center text-secondary py-4">No hay publicaciones pendientes</td></tr>`;
        return;
      }

      d.data.forEach(pub => {
        const row = document.createElement("tr");
        row.innerHTML = `
          <td><input type="checkbox" class="form-check-input"></td>
          <td>${pub.titulo}</td>
          <td class="d-none d-md-table-cell">${pub.autor}</td>
          <td>${pub.categoria}</td>
          <td class="d-none d-sm-table-cell">${pub.sede}</td>
          <td class="d-none d-lg-table-cell">${pub.creada_en}</td>
          <td><span class="ff-status-badge ff-status-${pub.estatus.toLowerCase()}">${pub.estatus}</span></td>
          <td class="text-end ff-action">
            <button class="btn btn-sm btn-outline-light" onclick="verPreview(${pub.publicacion_id})">👁️ Ver</button>
          </td>
        `;
        tbody.appendChild(row);
      });
    })
    .catch(err => console.error(err));
}

function verPreview(id) {
  // Aquí podrías hacer otra llamada para mostrar la publicación en el modal
  alert("Vista previa de la publicación ID: " + id);
}
