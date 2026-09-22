-- À installer dans un NOUVEAU projet Supabase avant activation des comptes.
-- Aucun accès administrateur ne peut être accordé depuis le navigateur.
begin;
create table public.ev_admins(user_id uuid primary key references auth.users(id) on delete cascade);
alter table public.ev_admins enable row level security;
revoke all on public.ev_admins from anon, authenticated;
create function public.ev_is_admin() returns boolean language sql stable security definer set search_path = '' as $$ select exists(select 1 from public.ev_admins where user_id = auth.uid()); $$;
revoke all on function public.ev_is_admin() from public;
grant execute on function public.ev_is_admin() to authenticated;
create table public.ev_clients(id uuid primary key default gen_random_uuid(),created_at timestamptz not null default now(),name text not null,email text not null,phone text default '',auth_user_id uuid unique references auth.users(id) on delete set null);
create table public.ev_projects(id uuid primary key default gen_random_uuid(),created_at timestamptz not null default now(),client_id uuid not null references public.ev_clients(id),title text not null,stage text not null default 'À cadrer' check(stage in ('À cadrer','En création','À valider','Livré')),due_date date,next_step text default '',summary text default '');
create table public.ev_requests(id uuid primary key default gen_random_uuid(),created_at timestamptz not null default now(),name text not null,email text not null,phone text default '',need text not null,status text not null default 'Nouvelle' check(status in ('Nouvelle','À recontacter','Devis envoyé','Gagnée','Classée')));
create table public.ev_tasks(id uuid primary key default gen_random_uuid(),created_at timestamptz not null default now(),owner_id uuid not null references auth.users(id),title text not null,due_date date,done boolean not null default false);
create table public.ev_notes(id uuid primary key default gen_random_uuid(),created_at timestamptz not null default now(),owner_id uuid not null references auth.users(id),title text not null,body text not null);
create table public.ev_messages(id uuid primary key default gen_random_uuid(),created_at timestamptz not null default now(),project_id uuid not null references public.ev_projects(id),sender_id uuid not null references auth.users(id),body text not null check(length(body) between 1 and 4000));
-- Aucune lecture ni écriture anonyme, y compris dans les demandes.
do $$ declare t text; begin foreach t in array array['ev_clients','ev_projects','ev_requests','ev_tasks','ev_notes','ev_messages'] loop
 execute format('alter table public.%I enable row level security',t);
 execute format('revoke all on public.%I from anon, authenticated',t);
 execute format('grant select, insert, update on public.%I to authenticated',t);
 end loop; end $$;
create policy clients_read on public.ev_clients for select to authenticated using(public.ev_is_admin() or auth_user_id=auth.uid());
create policy clients_insert on public.ev_clients for insert to authenticated with check(public.ev_is_admin());
create policy clients_update on public.ev_clients for update to authenticated using(public.ev_is_admin()) with check(public.ev_is_admin());
create policy projects_read on public.ev_projects for select to authenticated using(public.ev_is_admin() or exists(select 1 from public.ev_clients c where c.id=client_id and c.auth_user_id=auth.uid()));
create policy projects_insert on public.ev_projects for insert to authenticated with check(public.ev_is_admin());
create policy projects_update on public.ev_projects for update to authenticated using(public.ev_is_admin()) with check(public.ev_is_admin());
create policy requests_read on public.ev_requests for select to authenticated using(public.ev_is_admin());
create policy requests_insert on public.ev_requests for insert to authenticated with check(public.ev_is_admin());
create policy requests_update on public.ev_requests for update to authenticated using(public.ev_is_admin()) with check(public.ev_is_admin());
create policy tasks_read on public.ev_tasks for select to authenticated using(public.ev_is_admin() and owner_id=auth.uid());
create policy tasks_insert on public.ev_tasks for insert to authenticated with check(public.ev_is_admin() and owner_id=auth.uid());
create policy tasks_update on public.ev_tasks for update to authenticated using(public.ev_is_admin() and owner_id=auth.uid()) with check(public.ev_is_admin() and owner_id=auth.uid());
create policy notes_read on public.ev_notes for select to authenticated using(public.ev_is_admin() and owner_id=auth.uid());
create policy notes_insert on public.ev_notes for insert to authenticated with check(public.ev_is_admin() and owner_id=auth.uid());
create policy notes_update on public.ev_notes for update to authenticated using(public.ev_is_admin() and owner_id=auth.uid()) with check(public.ev_is_admin() and owner_id=auth.uid());
create policy messages_read on public.ev_messages for select to authenticated using(exists(select 1 from public.ev_projects p where p.id=project_id));
create policy messages_insert on public.ev_messages for insert to authenticated with check(sender_id=auth.uid() and exists(select 1 from public.ev_projects p where p.id=project_id));
revoke update on public.ev_messages from authenticated;
commit;
-- Dans l'éditeur SQL uniquement, après création du compte administrateur :
-- insert into public.ev_admins(user_id) values ('UUID_DU_COMPTE');
-- Associer ensuite ev_clients.auth_user_id à l'UUID du compte de chaque client.
