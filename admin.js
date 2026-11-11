function showSection(sectionId) {
    const sections = document.querySelectorAll("main > section");
    sections.forEach(section => section.style.display = "none");

    const activeSection = document.getElementById(sectionId);
    if (activeSection) {
        activeSection.style.display = "block";
    }
}

window.addEventListener("DOMContentLoaded", () => {
    showSection("dashboard");
});

function openEditModal(movieId, title, price, trailerUrl) {

    document.getElementById('edit_movie_id').value = movieId;
    document.getElementById('edit_movie_title').value = title;
    document.getElementById('edit_movie_price').value = price;
    document.getElementById('edit_trailer_url').value = trailerUrl;
    document.getElementById('editModal').classList.add("show");

}

function closeEditModal() {
    document.getElementById('editModal').classList.remove("show");
}

function openEditUpcomingMovieModal(id, title, trailerUrl) {
    document.getElementById('edit_upcoming_movie_id').value = id;
    document.getElementById('edit_upcoming_movie_title').value = title;
    document.getElementById('edit_upcoming_movie_trailer').value = trailerUrl;

    document.getElementById('editUpcomingMovieModal').classList.add("show");
}

function closeEditUpcomingMovieModal() {
    document.getElementById('editUpcomingMovieModal').classList.remove('show'); 
}

