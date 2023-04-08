<?php

declare(strict_types = 1);

/**
 * Copyright 2022-2023 Roman Ondráček
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *    https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace App\Models\Database\Entities;

use App\Models\Database\Attributes\TCreatedAt;
use App\Models\Database\Attributes\TUuid;
use App\Models\Database\Repositories\UserVerificationRepository;
use DateInterval;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * User verification
 */
#[ORM\Entity(repositoryClass: UserVerificationRepository::class)]
#[ORM\Table(name: 'email_verification')]
#[ORM\HasLifecycleCallbacks]
class UserVerification {

	use TUuid;
	use TCreatedAt;

	/**
	 * Constructor
	 * @param User $user User
	 */
	public function __construct(
		#[ORM\OneToOne(targetEntity: User::class, cascade: ['persist'])]
		#[ORM\JoinColumn(name: 'user', referencedColumnName: 'id', onDelete: 'CASCADE')]
		public readonly User $user,
	) {
	}

	/**
	 * Checks if the account verification is expired
	 * @return bool Is the account verification expired?
	 */
	public function isExpired(): bool {
		$expirationInterval = new DateInterval('P7D');
		$expiration = DateTimeImmutable::createFromMutable($this->createdAt)
			->add($expirationInterval);
		$now = new DateTimeImmutable();
		return $now >= $expiration;
	}

}
