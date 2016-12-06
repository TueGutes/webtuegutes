<?php
/**
 * Unterer Teil der Website
 *
 * Enthält den unteren Teil der Website (wie z.B. Footer)
 *
 * @author Henrik Huckauf <henrik.huckauf@stud.hs-hannover.de>
 */
 $url =(isset($_SERVER['HTTPS'])?'https':'http').'://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; 
?>
			</div>
			<div class="bottom">
				<footer>
					<div>
						<br><br>
						
						<!-- Your share button code -->
						<div class="fb-share-button" 
							data-href="https://www.facebook.com/tueGutesinHannover/"
							data-layout="button_count"> 
						</div>
						<br>
						<a href="./privacy"><?php echo $wlang['privacy']; ?></a><br>
						<a href="./imprint"><?php echo $wlang['imprint']; ?></a><br>
						<a href="./terms"><?php echo $wlang['terms']; ?></a>
					</div>
					<div class="copyright">                
						<p>COPYRIGHT © 2016 <a href="./">TueGutes</a></p>
					</div>
				</footer>
			</div>
		</div>
	</div>
</div>

</body>
</html>