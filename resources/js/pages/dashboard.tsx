import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/app-layout';
import { dashboard } from '@/routes';
import type { BreadcrumbItem, Todo } from '@/types';
import { Head, router, useForm } from '@inertiajs/react';
import { Filter, Loader2, Plus, Search, Trash2 } from 'lucide-react';
import { useDeferredValue, useEffect, useState } from 'react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

interface DashboardProps {
    todos: Todo[];
    filters: {
        status?: string;
        search?: string;
    };
}

export default function Dashboard({ todos, filters }: DashboardProps) {
    const { data, setData, post, processing, reset, errors } = useForm({
        title: '',
    });

    const [searchQuery, setSearchQuery] = useState(filters.search || '');
    const deferredSearch = useDeferredValue(searchQuery);

    useEffect(() => {
        if (deferredSearch !== filters.search) {
            router.get(
                route('dashboard'),
                { ...filters, search: deferredSearch },
                { preserveState: true, replace: true },
            );
        }
    }, [deferredSearch]);

    const handleAddTask = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('todos.store'), {
            onSuccess: () => reset(),
        });
    };

    const toggleTodo = (todo: Todo) => {
        router.patch(route('todos.update', todo.id), {
            is_completed: !todo.is_completed,
        });
    };

    const deleteTodo = (todo: Todo) => {
        if (confirm('Are you sure you want to delete this task?')) {
            router.delete(route('todos.destroy', todo.id));
        }
    };

    const setStatusFilter = (status: string | null) => {
        router.get(route('dashboard'), { ...filters, status }, { preserveState: true });
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Tasks" />
            <div className="flex flex-col gap-6 p-6 max-w-4xl mx-auto w-full animate-in fade-in duration-500">
                <header className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h1 className="text-3xl font-bold tracking-tight">Minhas Tarefas</h1>
                        <p className="text-muted-foreground">Gerencie seus afazeres diários com facilidade.</p>
                    </div>

                    <div className="relative w-full md:w-64">
                        <Search className="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
                        <Input
                            type="search"
                            placeholder="Buscar tarefas..."
                            className="pl-8 bg-background/50"
                            value={searchQuery}
                            onChange={(e) => setSearchQuery(e.target.value)}
                        />
                    </div>
                </header>

                <Card className="border-primary/10 shadow-sm bg-card/50 backdrop-blur-sm">
                    <CardHeader className="pb-3">
                        <CardTitle className="text-lg font-medium">Nova Tarefa</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <form onSubmit={handleAddTask} className="flex gap-2">
                            <div className="grid flex-1 gap-1">
                                <Input
                                    placeholder="O que precisa ser feito?"
                                    value={data.title}
                                    onChange={(e) => setData('title', e.target.value)}
                                    className={errors.title ? 'border-destructive' : ''}
                                />
                                {errors.title && <span className="text-xs text-destructive px-1">{errors.title}</span>}
                            </div>
                            <Button type="submit" disabled={processing} className="shrink-0">
                                {processing ? <Loader2 className="h-4 w-4 animate-spin" /> : <Plus className="h-4 w-4 mr-2" />}
                                Adicionar
                            </Button>
                        </form>
                    </CardContent>
                </Card>

                <div className="flex items-center justify-between border-b pb-4 mt-2">
                    <div className="flex items-center gap-1">
                        <Button
                            variant={!filters.status ? 'secondary' : 'ghost'}
                            size="sm"
                            onClick={() => setStatusFilter(null)}
                            className="h-8 px-3"
                        >
                            Todas
                        </Button>
                        <Button
                            variant={filters.status === 'pending' ? 'secondary' : 'ghost'}
                            size="sm"
                            onClick={() => setStatusFilter('pending')}
                            className="h-8 px-3"
                        >
                            Pendentes
                        </Button>
                        <Button
                            variant={filters.status === 'completed' ? 'secondary' : 'ghost'}
                            size="sm"
                            onClick={() => setStatusFilter('completed')}
                            className="h-8 px-3"
                        >
                            Concluídas
                        </Button>
                    </div>
                    <div className="text-xs text-muted-foreground font-medium">
                        {todos.length} {todos.length === 1 ? 'tarefa' : 'tarefas'} encontrada(s)
                    </div>
                </div>

                <div className="space-y-3 pb-20">
                    {todos.length > 0 ? (
                        todos.map((todo) => (
                            <div
                                key={todo.id}
                                className="group flex items-center justify-between p-4 rounded-xl border bg-card hover:border-primary/20 hover:shadow-md transition-all duration-200"
                            >
                                <div className="flex items-center gap-3 flex-1 min-w-0">
                                    <Checkbox
                                        id={`todo-${todo.id}`}
                                        checked={todo.is_completed}
                                        onCheckedChange={() => toggleTodo(todo)}
                                        className="h-5 w-5 rounded-md"
                                    />
                                    <Label
                                        htmlFor={`todo-${todo.id}`}
                                        className={`text-base font-normal cursor-pointer truncate transition-all duration-300 ${
                                            todo.is_completed ? 'text-muted-foreground line-through' : ''
                                        }`}
                                    >
                                        {todo.title}
                                    </Label>
                                </div>
                                <div className="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        onClick={() => deleteTodo(todo)}
                                        className="h-8 w-8 text-muted-foreground hover:text-destructive hover:bg-destructive/10"
                                    >
                                        <Trash2 className="h-4 w-4" />
                                    </Button>
                                </div>
                            </div>
                        ))
                    ) : (
                        <div className="flex flex-col items-center justify-center py-20 text-center animate-in zoom-in duration-300">
                            <div className="h-16 w-16 bg-muted/30 rounded-full flex items-center justify-center mb-4">
                                <Filter className="h-8 w-8 text-muted-foreground/50" />
                            </div>
                            <h3 className="font-medium text-lg">Nenhuma tarefa encontrada</h3>
                            <p className="text-muted-foreground max-w-xs mx-auto">
                                {filters.search ? `Não encontramos resultados para "${filters.search}"` : 'Comece adicionando sua primeira tarefa acima!'}
                            </p>
                            {filters.status || filters.search ? (
                                <Button variant="link" onClick={() => (window.location.href = route('dashboard'))} className="mt-2 text-primary">
                                    Limpar filtros
                                </Button>
                            ) : null}
                        </div>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}
