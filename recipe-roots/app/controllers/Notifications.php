<?php

class Notifications {
	use Controller;

	protected $profile;

	public function __construct() {
		if ( ! isAuthenticated() ) {
			handleUnauthenticated( $_GET['url'] );
		}
		$this->profile = $_SESSION['profile'];

		handleInvalidCsrfToken( $this );
	}

	public function index() {
		$notificationsModel = new Notification();

		if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
			$notificationId = $_POST['notificationId'] ?? null;

			if ( $notificationId ) {
				$notification = $notificationsModel->findById( $notificationId );

				if ( $notification ) {
					$notificationsModel->update(
						$notificationId,
						[ 'isRead' => $notification['isRead'] === 1 ? 0 : 1 ]
					);
				}
			} else {
				$notifications = $notificationsModel->findAll( [ 'receiverId' => $this->profile['id'] ] );
				foreach ( $notifications as $notification ) {
					$notificationsModel->update( $notification['id'], [ 'isRead' => 1 ] );
				}
			}
		}

		[ $currentPage, $totalPages, $notifications ] = getPaginationData(
			$notificationsModel,
			10,
			[ 'receiverId' => $this->profile['id'] ]
		);
		$this->view(
			'notifications',
			[ 
				'notifications' => $notifications,
				'currentPage' => $currentPage,
				'totalPages' => $totalPages
			]
		);
	}
}