</div>
</div>

<!-- ////////////////////////////////////////////////////////////////////////////-->
<footer class="footer footer-static footer-light navbar-border navbar-shadow">
    <p class="clearfix blue-grey lighten-2 text-sm-center mb-0 px-2">
        <span class="float-md-left d-block d-md-inline-block">Copyright &copy; 2018 <a class="text-bold-800 grey darken-2" href="https://citypost.com.vn" target="_blank">TRUNGTAMYTEMELINH.COM </a>, All rights reserved. </span>
    </p>
</footer>
<!-- BEGIN VENDOR JS-->
<!-- BEGIN VENDOR JS-->
<!-- BEGIN PAGE VENDOR JS-->
<script src="app-assets/vendors/js/charts/chart.min.js" type="text/javascript"></script>
<script src="app-assets/vendors/js/charts/echarts/echarts.js" type="text/javascript"></script>
<!-- END PAGE VENDOR JS-->

<!-- BEGIN PAGE VENDOR JS-->
<?php
foreach ($js_plus AS $js){
    echo '<script src="'. $js .'" type="text/javascript"></script>'."\n";
}
?>
<!-- BEGIN MODERN JS-->
<script src="app-assets/js/core/app-menu.js" type="text/javascript"></script>
<script src="app-assets/js/core/app.js" type="text/javascript"></script>
<script src="app-assets/js/scripts/customizer.js" type="text/javascript"></script>
<!-- END MODERN JS-->
<!-- PLUS -->
<script>tinymce.init({ selector:'textarea' });</script>
<!-- PLUS -->
<!--<script src="http://code.jquery.com/jquery-latest.min.js"></script>-->

</body>
</html>