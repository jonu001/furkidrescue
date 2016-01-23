<div class="container">
	<?php 

		$x = 0;
		foreach ($pet_general as $pet): 
			if (($x & 1) === 0):
				echo '<div class="row">';
			endif;
	?>
			<div class="col-sm-6">
				<section class="pet">
					<div class="top">
						<div class="thumb" <?php echo 'style="background: url(http://photos.petfinder.com/photos/pets/' . $pet['pet_id'] . '/1/?width=95&-fpm.jpg) 50% 50% no-repeat;"'; ?>>
						</div>
						<h4>
							<?php echo $pet['name']; ?>
						</h4>
						<h5>
							<?php
							$breed_list = '';
							foreach ($pet_breeds as $breed):
								if ($pet['pet_id'] === $breed['pet_id']):
									$breed_list .= $breed['breed'] . '&nbsp;&amp;&nbsp;';
								endif;
							endforeach;
							echo rtrim($breed_list, '&nbsp;&amp;&nbsp;');
							?>
						</h5>
						<h5>
							<?php 
							echo $pet['age'] . ' &#8226 ';
							switch($pet['sex']): 
								case 'M':
									echo 'Male';
									break;
								case 'F':
									echo 'Female';
									break;
								endswitch;
							echo ' &#8226 ';
							switch($pet['size']): 
								case 'S':
									echo 'Small';
									break;
								case 'M':
									echo 'Medium';
									break;
								case 'L':
									echo 'Large';
									break;
							endswitch;
							?> 
						</h5>
						<p>
							<a href="#">Click to read more.</a>
						</p>
					</div>
					<div class="bottom">
						<p>
							<?php echo $pet['description']; ?>
						</p>
						<a href="https://www.petfinder.com/petdetail/33338593">Click to read more.</a>
					</div>
				</section>
			</div>

	<?php
			if (($x & 1) === 1):
				echo '</div>';
			endif;
			$x++;		
		endforeach;
	?>
  
</div>