<div class="container">


	<?php 

		$x = 0;
		foreach ($pet_general as $pet): 

			if (($x & 1) === 0):
				echo '<div class="row">';
			endif;
	?>

				<div class="col-sm-6">
				<section class="pet_box" <?php echo 'style="background-image: url(\'http://photos.petfinder.com/photos/pets/' . $pet['pet_id'] . '/1/?width=500&-x.jpg\')"';?>>
					<div class="pane-info">		
						<div class="summary">
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
								<?php echo substr($pet['description'],0, 400) . '...'; ?>
							</p>
							<p>
								<a href="https://www.petfinder.com/petdetail/33338593">Click to read more.</a>
							</p>
						</div>
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