import { Head, Link, useForm } from '@inertiajs/react';

import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/app-layout';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';

type FormData = {
    name: string;
    slug: string;
};

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Blog', href: '/dashboard/blog' },
    { title: 'Tags', href: '/dashboard/blog/tags' },
    { title: 'Create', href: '/dashboard/blog/tags/create' },
];

export default function DashboardBlogTagsCreate() {
    const { data, setData, post, processing, errors } = useForm<FormData>({
        name: '',
        slug: '',
    });

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Blog - Create tag" />

            <div className="p-4">
                <Card className="py-0">
                    <CardHeader className="pt-6">
                        <CardTitle className="text-2xl">Create tag</CardTitle>
                        <CardDescription>Create a new tag.</CardDescription>
                    </CardHeader>
                    <CardContent className="pt-0">
                        <form
                            className="space-y-6"
                            onSubmit={(e) => {
                                e.preventDefault();
                                post('/dashboard/blog/tags');
                            }}
                        >
                            <div className="space-y-2">
                                <Label htmlFor="name">Name</Label>
                                <Input
                                    id="name"
                                    value={data.name}
                                    onChange={(e) =>
                                        setData('name', e.target.value)
                                    }
                                />
                                {errors.name ? (
                                    <div className="text-sm text-destructive">
                                        {errors.name}
                                    </div>
                                ) : null}
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="slug">Slug (optional)</Label>
                                <Input
                                    id="slug"
                                    value={data.slug}
                                    onChange={(e) =>
                                        setData('slug', e.target.value)
                                    }
                                />
                                {errors.slug ? (
                                    <div className="text-sm text-destructive">
                                        {errors.slug}
                                    </div>
                                ) : null}
                            </div>

                            <div className="flex items-center justify-between gap-3">
                                <Button asChild variant="outline">
                                    <Link href="/dashboard/blog/tags">Cancel</Link>
                                </Button>

                                <Button type="submit" disabled={processing}>
                                    Create
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
