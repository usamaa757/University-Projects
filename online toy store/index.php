<?php require('header.php'); ?>
<style>
  .mySlides {
    display: all;
  }
</style>

<div class="container">
  <div>
    <br><br><br>

    <div class="" align="center" style="float:center;">
      <img class="mySlides" src="images/cubix.jpg" style="width:300px;height:300px">
      <img class="mySlides" src="images/barbie.jpg" style="width:300px;height:300px">
      <img class="mySlides" src="images/car.jpg" style="width:300px;height:300px">
      <img class="mySlides" src="images/doll.jpg" style="width:300px;height:300px">
      <img class="mySlides" src="images/truck.jpg" style="width:300px;height:300px">
      <img class="mySlides" src="images/truck.jpg" style="width:300px;height:300px">
    </div>


    <h3>
      <div align="center"><a href="/login.php">Login to Browse Toys</a></div>
    </h3><br><br>
  </div>
</div>
<script>
  var myIndex = 0;
  carousel();

  function carousel() {
    var i;
    var x = document.getElementsByClassName("mySlides");
    for (i = 0; i < x.length; i++) {
      x[i].style.display = "none";
    }
    myIndex++;
    if (myIndex > x.length) {
      myIndex = 1
    }
    x[myIndex - 1].style.display = "block";
    setTimeout(carousel, 1500);
  }
</script>
<?php require('footer.php'); ?>