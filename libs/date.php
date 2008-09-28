<?php
	function NowDate() {
		return date( 'Y-m-d H:i:s', time() );
	}

	function dateDiffText( $dateTimeBegin ) {
		$diff = dateDiff( $dateTimeBegin );

		if ( !empty( $diff[ 'years' ] ) ) {
			if ( $diff[ 'year' ] == 1 ) {
				return 'last year';
			}
			return $diff[ 'years' ] . ' years ago';
		}
		if ( !empty( $diff[ 'months' ] ) ) {
			if ( $diff[ 'months' ] == 1 ) {
				return 'last month';
			}
			return $diff[ 'months' ] . ' months ago';
		}
		if ( !empty( $diff[ 'weeks' ] ) ) {
			if ( $diff[ 'weeks' ] == 1 ) {
				return 'last week';
			}
			return $diff[ 'weeks' ] . ' weeks  ago';
		}
		if ( !empty( $diff[ 'days' ] ) ) {
			if ( $diff[ 'days' ] == 1 ) {
				return 'yesterday';
			}
			return $diff[ 'days' ] . ' days ago';
		}
		if ( !empty( $diff[ 'hours' ] ) ) {
			if ( $diff[ 'hours' ] == 1 ) {
				return '1 hour ago';
			}
			return $diff[ 'hours' ] . ' hours ago';
		}
		if ( !empty( $diff[ 'minutes' ] ) ) {
			if ( $diff[ 'minutes' ] == 1 ) {
				return 'a minute ago';
			}
			return $diff[ 'minutes' ] . ' minutes ago';
		}
		return 'just now';
	}

	function dateDiff( $dateTimeBegin, $dateTimeEnd = NULL ) {
		if ( !isset( $dateTimeEnd ) ) {
			$dateTimeEnd = NowDate();
		}

		if ( !$dateTimeBegin || !$dateTimeEnd ) {
			// error condition
			return false;
		}

		$dateTimeBegin = strtotime( $dateTimeBegin );
		$dateTimeEnd = strtotime( $dateTimeEnd );

		if ( $dateTimeEnd === -1 || $dateTimeBegin === -1 ) {
			// error condition
			return false;
		}

		$diff = $dateTimeEnd - $dateTimeBegin;

		if ( $diff < 0 ) {
			// error condition
			return false;
		}

		$weeks = $days = $hours = $minutes = $seconds = 0; // initialize vars

		if ( $diff % 604800 > 0 ) {
			$rest1 = $diff % 604800;
			$weeks = ( $diff - $rest1 ) / 604800; // seconds a week
			if ( $rest1 % 86400 > 0 ) {
				$rest2 = ( $rest1 % 86400 );
				$days = ( $rest1 - $rest2 ) / 86400; // seconds a day
				if ( $rest2 % 3600 > 0 ) {
					$rest3 = ( $rest2 % 3600 );
					$hours = ( $rest2 - $rest3 ) / 3600; // seconds an hour
					if ( $rest3 % 60 > 0 ) {
						$seconds = ( $rest3 % 60 );
						$minutes = ( $rest3 - $seconds ) / 60; // seconds a minute
					} 
					else {
						$minutes = $rest3 / 60;
					}
				}
				else {
					$hours = $rest2 / 3600;
				}
			}
			else {
				$days = $rest1 / 86400;
			}
		}
		else {
			$weeks = $diff / 604800;
		}
		if ( $weeks ) {
			$hours = 0;
		}
		if ( $days || $weeks ) {
			$minutes = 0;
		}
		$months = floor( $weeks / 4 );
		if ( $months ) {
			$weeks -= $months * 4;
		}
		$years = floor( $months / 12 );
		if ( $years ) {
			$months -= $years * 12;
		}

		$result = array(
			'years' => $years,
			'months' => $months,
			'weeks' => $weeks,
			'days' => $days,
			'hours' => $hours,
			'minutes' => $minutes,
		);

		return $result;
	}
?>
