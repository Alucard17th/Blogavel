import { Head, Link, useForm } from '@inertiajs/react';
import { useMemo } from 'react';

import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/app-layout';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';

type Option = {
    id: number;
    name: string;
};

type PostPayload = {
    id: number;
    title: string;
    content: string | null;
    status: 'draft' | 'scheduled' | 'published';
    published_at: string | null;
    category_id: number | null;
    tags: number[];
};

type FormData = {
    title: string;
    content: string;
    status: 'draft' | 'scheduled' | 'published';
    published_at: string;
    category_id: string;
    tags: number[];
};

export default function DashboardBlogPostsEdit({
    post,
    categories,
    tags,
}: {
    post: PostPayload;
    categories: Option[];
    tags: Option[];
}) {
    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Dashboard', href: dashboard().url },
        { title: 'Blog', href: '/dashboard/blog' },
        { title: 'Posts', href: '/dashboard/blog/posts' },
        { title: 'Edit', href: `/dashboard/blog/posts/${post.id}/edit` },
    ];

    const { data, setData, put, processing, errors } = useForm<FormData>({
        title: post.title,
        content: post.content ?? '',
        status: post.status,
        published_at: post.published_at ?? '',
        category_id: post.category_id ? String(post.category_id) : '',
        tags: Array.isArray(post.tags) ? post.tags : [],
    });

    const categoryValue = useMemo(
        () => (data.category_id === '' ? undefined : data.category_id),
        [data.category_id],
    );

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Blog - Edit post" />

            <div className="p-4">
                <Card className="py-0">
                    <CardHeader className="pt-6">
                        <CardTitle className="text-2xl">Edit post</CardTitle>
                        <CardDescription>Update post #{post.id}.</CardDescription>
                    </CardHeader>
                    <CardContent className="pt-0">
                        <form
                            className="space-y-6"
                            onSubmit={(e) => {
                                e.preventDefault();
                                put(`/dashboard/blog/posts/${post.id}`);
                            }}
                        >
                            <div className="space-y-2">
                                <Label htmlFor="title">Title</Label>
                                <Input
                                    id="title"
                                    value={data.title}
                                    onChange={(e) =>
                                        setData('title', e.target.value)
                                    }
                                />
                                {errors.title ? (
                                    <div className="text-sm text-destructive">
                                        {errors.title}
                                    </div>
                                ) : null}
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="content">Content</Label>
                                <Textarea
                                    id="content"
                                    value={data.content}
                                    onChange={(e) =>
                                        setData('content', e.target.value)
                                    }
                                />
                                {errors.content ? (
                                    <div className="text-sm text-destructive">
                                        {errors.content}
                                    </div>
                                ) : null}
                            </div>

                            <div className="grid gap-4 md:grid-cols-2">
                                <div className="space-y-2">
                                    <Label>Status</Label>
                                    <Select
                                        value={data.status}
                                        onValueChange={(value) =>
                                            setData(
                                                'status',
                                                value as FormData['status'],
                                            )
                                        }
                                    >
                                        <SelectTrigger>
                                            <SelectValue placeholder="Select status" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="draft">
                                                draft
                                            </SelectItem>
                                            <SelectItem value="scheduled">
                                                scheduled
                                            </SelectItem>
                                            <SelectItem value="published">
                                                published
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                    {errors.status ? (
                                        <div className="text-sm text-destructive">
                                            {errors.status}
                                        </div>
                                    ) : null}
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="published_at">
                                        Published at
                                    </Label>
                                    <Input
                                        id="published_at"
                                        type="datetime-local"
                                        value={data.published_at}
                                        onChange={(e) =>
                                            setData(
                                                'published_at',
                                                e.target.value,
                                            )
                                        }
                                    />
                                    {errors.published_at ? (
                                        <div className="text-sm text-destructive">
                                            {errors.published_at}
                                        </div>
                                    ) : null}
                                </div>
                            </div>

                            <div className="space-y-2">
                                <Label>Category</Label>
                                <Select
                                    value={categoryValue}
                                    onValueChange={(value) =>
                                        setData('category_id', value)
                                    }
                                >
                                    <SelectTrigger>
                                        <SelectValue placeholder="No category" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {categories.map((c) => (
                                            <SelectItem
                                                key={c.id}
                                                value={String(c.id)}
                                            >
                                                {c.name}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                                {errors.category_id ? (
                                    <div className="text-sm text-destructive">
                                        {errors.category_id}
                                    </div>
                                ) : null}
                            </div>

                            <div className="space-y-2">
                                <Label>Tags</Label>
                                <div className="grid gap-2 md:grid-cols-2">
                                    {tags.map((t) => {
                                        const checked = data.tags.includes(t.id);
                                        return (
                                            <label
                                                key={t.id}
                                                className="flex items-center gap-2 rounded-md border p-2"
                                            >
                                                <Checkbox
                                                    checked={checked}
                                                    onCheckedChange={(v) => {
                                                        const isChecked =
                                                            v === true;
                                                        setData(
                                                            'tags',
                                                            isChecked
                                                                ? [
                                                                      ...data.tags,
                                                                      t.id,
                                                                  ]
                                                                : data.tags.filter(
                                                                      (id) =>
                                                                          id !==
                                                                          t.id,
                                                                  ),
                                                        );
                                                    }}
                                                />
                                                <span className="text-sm">
                                                    {t.name}
                                                </span>
                                            </label>
                                        );
                                    })}
                                </div>
                                {errors.tags ? (
                                    <div className="text-sm text-destructive">
                                        {errors.tags}
                                    </div>
                                ) : null}
                            </div>

                            <div className="flex items-center justify-between gap-3">
                                <Button asChild variant="outline">
                                    <Link href="/dashboard/blog/posts">
                                        Cancel
                                    </Link>
                                </Button>

                                <div className="flex items-center gap-3">
                                    <Button asChild variant="link" className="px-0">
                                        <Link href={dashboard()}>Dashboard</Link>
                                    </Button>
                                    <Button type="submit" disabled={processing}>
                                        Save
                                    </Button>
                                </div>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
