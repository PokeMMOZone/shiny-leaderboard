<!-- nav.php -->
<nav>
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="player-leaderboard.php">Player Leaderboard</a></li>
        <li><a href="faq.php">FAQ</a></li>
        <li><a href="about.php">About</a></li>
        <!-- Add more pages as needed -->
    </ul>
</nav>

<div id="flashing-message">
    <a href="https://shinyboard.pokemmo.zone/" target="_blank">Shiny Board has been released!</a>
</div>

<!-- Modal Dialog -->
<div id="messageModal" class="modal">
    <div class="modal-content">
        <p>Shiny Board has been released!</p>
        <p>Click <a href="https://pokemmo.zone/?p=298" target="_blank">here</a> for more information.</p>
        <p>Shiny Board: <a href="https://shinyboard.pokemmo.zone/" target="_blank">Link</a></p>
        <p>Updates for this website has been reduced to once a day. This website will be going away by the end of the month!</p>
        <button id="closeModal">OK</button>
    </div>
</div>

<style>
/* Modal styles */
.modal {
    display: none; /* Hidden by default */
    position: fixed; /* Stay in place */
    z-index: 1; /* Sit on top */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    overflow: auto; /* Enable scroll if needed */
    background-color: rgb(0,0,0); /* Fallback color */
    background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

.modal-content {
    background-color: #121212;
    margin: 15% auto; /* 15% from the top and centered */
    padding: 20px;
    border: 1px solid #888;
    width: 80%; /* Could be more or less, depending on screen size */
    text-align: center;
}

#closeModal {
    background-color: #4CAF50; /* Green */
    color: white;
    padding: 10px 20px;
    border: none;
    cursor: pointer;
}

#closeModal:hover {
    background-color: #45a049;
}
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {
    var modal = document.getElementById("messageModal");
    var closeModal = document.getElementById("closeModal");

    // Show the modal
    modal.style.display = "block";

    // When the user clicks on the button, close the modal
    closeModal.onclick = function() {
        modal.style.display = "none";
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
});
</script>