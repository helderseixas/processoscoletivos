<div class="login-form-container">
    <?php if ( $attributes['show_title'] ) : ?>
        <h2><?php _e( 'Sign In', 'personalize-login' ); ?></h2>
    <?php endif; ?>
	     
	<!-- Show errors if there are any -->
	<?php if ( count( $attributes['errors'] ) > 0 ) : ?>
	    <?php foreach ( $attributes['errors'] as $error ) : ?>
	        <p class="login-error">
	            <?php echo $error; ?>
	        </p>
	    <?php endforeach; ?>
	<?php endif; ?>
	
	<!-- Show logged out message if user just logged out -->
	<?php if ( $attributes['logged_out'] ) : ?>
	    <p class="login-info">
	        <?php _e( 'You have signed out. Would you like to sign in again?', 'personalize-login' ); ?>
	    </p>
	<?php endif; ?>
	
	<?php if ( $attributes['registered'] ) : ?>
		<p class="login-info">
			<?php
				printf(
					__( 'You have successfully registered to <strong>%s</strong>. We have emailed your password to the email address you entered.', 'personalize-login' ),
					get_bloginfo( 'name' )
				);
			?>
		</p>
	<?php endif; ?>	
	
	Teste do certificado social.
</div>
