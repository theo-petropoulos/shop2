<?php if($confirm === 'pending'){ ?>
<h3>Votre inscription a bien été enregistrée</h3>

<p>Dernière étape, veuillez suivre le lien de validation qui vient de vous être envoyé sur votre boite mail afin d'activer votre compte.<br>
Attention : Le lien n'est valide que pendant 1 heure.<br>
Passé ce délai, vous devrez contacter l'assistance technique pour activer votre compte à l'adresse <a href="mailto:assistance@shop.com">assistance@shop.com</a></p>

<?php } else if($confirm === 'success'){ ?>
<h3>Bienvenue parmi nous !</h3>

<p>Féliciations, votre compte est désormais validé. Vous pouvez dès à présent accéder à votre profil et naviguer dans notre sélection de jeux !<br>
En vous souhaitant bon shopping ;)</p>
<?php } ?>