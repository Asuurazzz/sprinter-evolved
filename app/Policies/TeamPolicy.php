<?php

namespace App\Policies;

use App\Models\Team;
use App\Models\User;

class TeamPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Qualquer usuário autenticado pode listar times
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Team $team): bool
    {
        // Pode ver se:
        // - É membro ativo do time, OU
        // - Time é público
        return $team->isPublic() || $this->isActiveMember($user, $team);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Qualquer usuário autenticado pode criar times
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Team $team): bool
    {
        // Pode atualizar se é membro com permissão de update (OWNER ou ADMIN)
        $member = $team->getActiveMember($user->id);

        return $member && $member->canUpdate();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Team $team): bool
    {
        // Apenas owner pode deletar o time
        return $team->isOwnedBy($user->id);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Team $team): bool
    {
        // Apenas owner pode restaurar o time
        return $team->isOwnedBy($user->id);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Team $team): bool
    {
        // Apenas owner pode deletar permanentemente
        return $team->isOwnedBy($user->id);
    }

    // ========================================
    // Custom Authorization Methods
    // ========================================

    /**
     * Determine whether the user can view team members.
     */
    public function viewMembers(User $user, Team $team): bool
    {
        // Pode ver membros se:
        // - É membro ativo do time, OU
        // - Time é público
        return $team->isPublic() || $this->isActiveMember($user, $team);
    }

    /**
     * Determine whether the user can invite members to the team.
     */
    public function invite(User $user, Team $team): bool
    {
        // Pode convidar se é membro com permissão de invite (OWNER ou ADMIN)
        $member = $team->getActiveMember($user->id);

        return $member && $member->canInvite();
    }

    /**
     * Determine whether the user can remove a member from the team.
     */
    public function removeMember(User $user, Team $team): bool
    {
        // Pode remover se é membro com permissão de remove (OWNER ou ADMIN)
        $member = $team->getActiveMember($user->id);

        return $member && $member->canRemove();
    }

    /**
     * Determine whether the user can manage team settings.
     */
    public function manageSettings(User $user, Team $team): bool
    {
        // Apenas owner pode gerenciar configurações
        $member = $team->getActiveMember($user->id);

        return $member && $member->canManageSettings();
    }

    /**
     * Determine whether the user can approve join requests.
     */
    public function approveJoinRequest(User $user, Team $team): bool
    {
        // Pode aprovar se é membro com permissão (OWNER ou ADMIN)
        $member = $team->getActiveMember($user->id);

        return $member && $member->canUpdate();
    }

    /**
     * Determine whether the user can leave the team.
     */
    public function leave(User $user, Team $team): bool
    {
        // Pode sair se é membro mas NÃO é owner
        return $this->isActiveMember($user, $team) && ! $team->isOwnedBy($user->id);
    }

    // ========================================
    // Helper Methods
    // ========================================

    /**
     * Check if user is an active member of the team.
     */
    protected function isActiveMember(User $user, Team $team): bool
    {
        return $team->getActiveMember($user->id) !== null;
    }
}
