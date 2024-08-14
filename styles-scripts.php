<!-- styles-scripts.php -->
<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function () {
        $('#leaderboard').DataTable({
            "paging": false,
            "responsive": true,
            "searching": true,
            "info": true,
            "language": {
                "info": "Displaying _TOTAL_ entries",
            },
            "order": [[4, "desc"]],
            "autoWidth": false
        });
        $('#userLeaderboard').DataTable({
            "paging": false,
            "responsive": true,
            "searching": true,
            "info": true,
            "language": {
                "info": "Displaying _TOTAL_ entries",
            },
            "order": [[2, "desc"]],
            "autoWidth": false
        });
    });
</script>