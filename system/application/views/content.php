<span id="tagline">Where do you want to go today?</span><br/>
(Location should be City, State format.)
<?php if(isset($errors)) echo $errors; ?>
<?php echo $form; ?>

<?php if(count($airportInfo) >= 1 || count($drivingInfo) >= 1) { ?>
<hr>
<?php } ?>

<?php if(count($airportInfo) >= 1) { ?>
<div class="infobox">
<p class="cost">$<?php echo $airportInfo[0]['data']['price'] ?></p>
<h2>Airline Cost</h2>
<p><b>From:</b> <?php echo $airportInfo[0]['from'] ?> <b>To:</b> <?php echo $airportInfo[0]['to'] ?><br/>
<b>Flight Number:</b> <?php echo $airportInfo[0]['data']['flight_number'] ?>
</div>
<?php } ?>

<?php if($drivingInfo['price'] != NULL) { ?>
<div class="infobox">
<p class="cost"><?php echo $drivingInfo['price'] ?></p>
<h2>Driving Cost</h2>
<p><b>From:</b> <?php echo $drivingInfo['from'] ?> <b>To:</b> <?php echo $drivingInfo['to'] ?></p>
</div>
<?php } ?>

<?php if(count($busInfo) >= 1) { ?>
<div class="infobox">
<p class="cost">$<?php echo $busInfo[0]['price'] ?></p>
<h2>Bus Cost</h2>
<p><b>From:</b> <?php echo $busInfo[0]['from']['name'] ?> <b>To:</b> <?php echo $busInfo[0]['to']['name'] ?></p>
</div>
<?php } ?>

