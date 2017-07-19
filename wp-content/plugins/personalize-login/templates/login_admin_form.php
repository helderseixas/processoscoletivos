<div class="login-form-container">
    <?php if ( $attributes['show_title'] ) : ?>
        <h2><?php _e( 'Acessar Sistema - Administradores', 'personalize-login' ); ?></h2>
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
	        <?php _e( 'Você saiu do sistema. Deseja entrar novamente?', 'personalize-login' ); ?>
	    </p>
	<?php endif; ?>
	
    <?php
        wp_login_form(
            array(                
                'label_log_in' => __( 'Entrar', 'personalize-login' ),
                'redirect' => $attributes['redirect'],
            )
        );
    ?>
     
    <a class="forgot-password" href="<?php echo wp_lostpassword_url(); ?>">
        <?php _e( 'Esqueceu a senha?', 'personalize-login' ); ?>
    </a>
</div>
