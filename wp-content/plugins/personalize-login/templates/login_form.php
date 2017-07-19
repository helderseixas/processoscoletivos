<div class="login-form-container">
    <?php if ( $attributes['show_title'] ) : ?>
        <h2><?php _e( 'Acessar Sistema', 'personalize-login' ); ?></h2>
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
	
	Informe seu Certificado Social para acessar o sistema Processos Coletivos.<br/><br/>	
	<form action="http://localhost:8080/certificadora-social-oauth2/login.jsp" method="POST">
		<input type="hidden" name="client_id" id="client_id" value="exemploaplicativocliente">
		<input type="hidden" name="client_secret" id="client_secret" value="9834ba657bb2c60b5bb53de6f4201905">
		<input type="submit" value="Informar Certificado Social" />
	</form>
</div>
