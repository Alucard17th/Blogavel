import { Head, Link } from '@inertiajs/react';
import { useEffect, useMemo, useState } from 'react';

import { dashboard } from '@/routes';

type Post = {
    id: number;
    title: string;
    slug: string;
    content?: string | null;
    status?: string;
    published_at?: string | null;
    category?: { id: number; name: string; slug: string } | null;
    tags?: Array<{ id: number; name: string; slug: string }>;
    featured_media?: { id: number; url: string } | null;
};

type PaginatedResponse<T> = {
    data: T[];
    links?: unknown;
    meta?: unknown;
};

export default function BlogIndex({ api_base }: { api_base: string }) {
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);
    const [posts, setPosts] = useState<Post[]>([]);

    const postsUrl = useMemo(() => `${api_base}/posts`, [api_base]);

    useEffect(() => {
        let cancelled = false;

        async function load() {
            setLoading(true);
            setError(null);

            try {
                const res = await fetch(postsUrl, {
                    headers: {
                        Accept: 'application/json',
                    },
                });

                if (!res.ok) {
                    throw new Error(`Request failed: ${res.status}`);
                }

                const json = (await res.json()) as PaginatedResponse<Post>;

                if (!cancelled) {
                    setPosts(Array.isArray(json?.data) ? json.data : []);
                }
            } catch (e) {
                if (!cancelled) {
                    setError(e instanceof Error ? e.message : 'Failed to load posts');
                }
            } finally {
                if (!cancelled) {
                    setLoading(false);
                }
            }
        }

        void load();

        return () => {
            cancelled = true;
        };
    }, [postsUrl]);

    return (
        <>
            <Head title="Blog" />

            <div className="min-h-screen bg-background">
                <header className="border-b">
                    <div className="mx-auto flex max-w-5xl items-center justify-between px-6 py-5">
                        <div>
                            <h1 className="text-xl font-semibold">Blog</h1>
                            <p className="text-sm text-muted-foreground">
                                Powered by Blogavel API
                            </p>
                        </div>
                        <Link
                            href={dashboard()}
                            className="rounded-md border px-3 py-2 text-sm"
                        >
                            Dashboard
                        </Link>
                    </div>
                </header>

                <main className="mx-auto max-w-5xl px-6 py-8">
                    {loading ? (
                        <div className="text-sm text-muted-foreground">
                            Loading posts...
                        </div>
                    ) : error ? (
                        <div className="rounded-md border border-destructive/30 bg-destructive/5 p-4 text-sm">
                            {error}
                        </div>
                    ) : posts.length === 0 ? (
                        <div className="text-sm text-muted-foreground">
                            No posts found.
                        </div>
                    ) : (
                        <div className="grid gap-4">
                            {posts.map((post) => (
                                <article
                                    key={post.id}
                                    className="overflow-hidden rounded-lg border bg-card"
                                >
                                    {post.featured_media?.url ? (
                                        <div className="aspect-[16/6] w-full overflow-hidden bg-muted">
                                            <img
                                                src={post.featured_media.url}
                                                alt={post.title}
                                                className="h-full w-full object-cover"
                                                loading="lazy"
                                            />
                                        </div>
                                    ) : null}

                                    <div className="p-5">
                                        <div className="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-muted-foreground">
                                            {post.category?.name ? (
                                                <span className="rounded-full border px-2 py-0.5">
                                                    {post.category.name}
                                                </span>
                                            ) : null}
                                            {post.published_at ? (
                                                <span>
                                                    {new Date(
                                                        post.published_at,
                                                    ).toLocaleDateString()}
                                                </span>
                                            ) : null}
                                        </div>

                                        <h2 className="mt-2 text-lg font-semibold leading-snug">
                                            {post.title}
                                        </h2>

                                        <p className="mt-2 line-clamp-3 text-sm text-muted-foreground">
                                            {post.content || ''}
                                        </p>

                                        <div className="mt-4 flex flex-wrap gap-2">
                                            {(post.tags || []).slice(0, 6).map((tag) => (
                                                <span
                                                    key={tag.id}
                                                    className="rounded-full bg-muted px-2 py-0.5 text-xs"
                                                >
                                                    #{tag.name}
                                                </span>
                                            ))}
                                        </div>
                                    </div>
                                </article>
                            ))}
                        </div>
                    )}
                </main>
            </div>
        </>
    );
}
