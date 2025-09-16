    <footer class="main-footer">
        <div class="pull-right hidden-xs">
          <b>Proz API Manager</b>管理画面
        </div>
        <strong>Copyright &copy; 2025 <a href="<?php echo base_url(); ?>"></a>.</strong> All rights reserved.
    </footer>
    <script src="<?php echo base_url(); ?>assets/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/dist/js/adminlte.min.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>assets/js/jquery.validate.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>assets/js/validation.js" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootoast@1.0.1/dist/bootoast.min.js"></script>
    <script>
      function adjustContentMargin() {
          const header = document.querySelector('.main-header');
          const content = document.querySelector('.content-wrapper');

          if (header && content) {
              const headerHeight = header.offsetHeight;
              // content.style.marginTop = headerHeight + 30 + 'px';
              // content.style.marginBottom = '30px';
          }
      }

      // Run on page load 
      window.addEventListener('load', adjustContentMargin);

      // Optional: Run on window resize
      window.addEventListener('resize', adjustContentMargin);
  </script>
  </body>
</html>